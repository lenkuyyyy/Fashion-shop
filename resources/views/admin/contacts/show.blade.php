@extends('admin.layouts.AdminLayouts')

@section('content')
<div class="container my-5">
    <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0 fw-bold">Chi tiết liên hệ</h3>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong class="me-2">ID:</strong> {{ $contact->id }}
            </p>
            <p class="mb-2">
                <strong class="me-2">Tên:</strong> {{ $contact->name }}
            </p>
            <p class="mb-2">
                <strong class="me-2">Email:</strong>
                {{ $contact->email }}
            </p>
            <p class="mb-2">
                <strong class="me-2">Nội dung:</strong>
                {{ $contact->message }}
            </p>
            <p class="mb-2">
                <strong class="me-2">Ngày gửi:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <div class="card-footer text-end bg-light">
            <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-primary fw-bold">
                <i class="bi bi-arrow-left-circle me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>
@endsection
