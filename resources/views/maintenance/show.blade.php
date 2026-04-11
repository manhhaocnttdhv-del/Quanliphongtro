@extends('layouts.user')

@section('title', 'Chi tiết yêu cầu bảo trì')

@section('content')
<div class="contact" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage mb-4">
                    <h2>Chi tiết yêu cầu #{{ $maintenanceRequest->id }}</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">{{ $maintenanceRequest->title }}</h4>
                        <p class="text-muted mb-4">{{ $maintenanceRequest->description }}</p>

                        @if($maintenanceRequest->images && count($maintenanceRequest->images) > 0)
                            <h6 class="fw-bold mb-3">Hình ảnh đính kèm:</h6>
                            <div class="row g-2">
                                @foreach($maintenanceRequest->images as $img)
                                    <div class="col-md-4 mb-3">
                                        <a href="{{ asset('storage/' . $img) }}" class="fancybox" rel="gallery">
                                            <img src="{{ asset('storage/' . $img) }}" class="img-fluid rounded shadow-sm" alt="Maintenance Image">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                @if($maintenanceRequest->admin_notes)
                    <div class="card shadow-sm border-0 border-left-primary bg-light">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-2"><i class="fa fa-commenting mr-2"></i>Phản hồi từ quản lý:</h6>
                            <p class="mb-0">{{ $maintenanceRequest->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold">Thông tin chung</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Trạng thái:</span>
                                <span class="badge badge-{{ $maintenanceRequest->statusBadge() }}">{{ $maintenanceRequest->statusLabel() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Mức độ:</span>
                                @php
                                    $prioClass = match($maintenanceRequest->priority) {
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        'low' => 'info',
                                        default => 'secondary'
                                    };
                                    $prioLabel = match($maintenanceRequest->priority) {
                                        'high' => 'Cao',
                                        'medium' => 'Trung bình',
                                        'low' => 'Thấp',
                                        default => $maintenanceRequest->priority
                                    };
                                @endphp
                                <span class="badge badge-{{ $prioClass }}">{{ $prioLabel }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Phòng:</span>
                                <span class="fw-bold">{{ $maintenanceRequest->room->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Ngày gửi:</span>
                                <span>{{ $maintenanceRequest->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                            @if($maintenanceRequest->status === 'resolved')
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Ngày hoàn thành:</span>
                                <span>{{ $maintenanceRequest->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer bg-white pt-3">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary btn-block">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
