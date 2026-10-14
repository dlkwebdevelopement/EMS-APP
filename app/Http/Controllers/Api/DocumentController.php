<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $employee = $request->user();
            $documents = EmployeeDocument::where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type' => $doc->document_type,
                        'title' => $doc->title,
                        'document_number' => $doc->document_number,
                        'file_name' => $doc->file_name,
                        'file_url' => $doc->file_url,
                        'status' => $doc->status,
                        'rejection_reason' => $doc->rejection_reason,
                        'created_at' => $doc->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'documents' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = $request->user();
        $file = $request->file('document_file');
        $filePath = $file->store("employee_documents/{$employee->id}", 'public');

        $docType = $request->document_type;
        $title = $request->title;

        try {
            $doc = EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $docType,
                'title' => $title,
                'document_number' => $request->document_number,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            $fallbackType = in_array($docType, ['contract', 'id_proof', 'certificate', 'experience_letter', 'resume', 'other']) ? $docType : 'other';
            $doc = EmployeeDocument::create([
                'employee_id' => $employee->id,
                'document_type' => $fallbackType,
                'title' => "[" . strtoupper($docType) . "] " . $title,
                'document_number' => $request->document_number,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully and pending verification.',
            'document' => [
                'id' => $doc->id,
                'document_type' => $doc->document_type,
                'title' => $doc->title,
                'file_url' => $doc->file_url,
                'status' => $doc->status
            ]
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $employee = $request->user();
        $doc = EmployeeDocument::where('employee_id', $employee->id)->where('id', $id)->first();

        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.'
        ]);
    }
}
