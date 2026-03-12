@extends('layouts.admin')
@section('title', 'Thêm Phòng Mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 mb-1">Thêm phòng mới</h1>
    </div>
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.rooms._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-plus me-1"></i>Thêm phòng</button>
            </div>
        </form>
    </div>
</div>
@endsection
