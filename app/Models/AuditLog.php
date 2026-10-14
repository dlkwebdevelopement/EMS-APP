<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'action', 
        'description', 'model_type', 'model_id', 
        'changes', 'ip_address'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        if ($this->user_type === 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        }
        return $this->belongsTo(Employee::class, 'user_id');
    }

    /**
     * Helper to log an activity
     */
    public static function log($action, $description, $model = null, $changes = null)
    {
        $user = auth()->guard('admin')->user() ?? auth()->guard('employee')->user();
        
        return self::create([
            'user_type' => auth()->guard('admin')->check() ? 'admin' : 'employee',
            'user_id' => $user->id ?? 0,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'changes' => $changes,
            'ip_address' => request()->ip()
        ]);
    }
}
