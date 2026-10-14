@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Client</h2>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $client->name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $client->company_name) }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Personal Details</label>
                        <textarea name="personal_details" class="form-control" rows="3">{{ old('personal_details', $client->personal_details) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Client</button>
            </form>
        </div>
    </div>
</div>
@endsection
