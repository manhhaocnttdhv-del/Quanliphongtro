@extends('layouts.user')

@section('title', 'Yêu cầu bảo trì')

@section('content')
<div class="contact" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <h2>Danh sách yêu cầu bảo trì</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Các yêu cầu đã gửi</h5>
                        <a href="{{ route('maintenance.create') }}" class="btn btn-primary" style="background:#fe0000; border:none;">Gửi yêu cầu mới</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4">Ngày gửi</th>
                                        <th class="border-0">Phòng</th>
                                        <th class="border-0">Vấn đề</th>
                                        <th class="border-0 text-center">Mức độ</th>
                                        <th class="border-0 text-center">Trạng thái</th>
                                        <th class="border-0 text-right px-4">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $req)
                                        <tr>
                                            <td class="px-4 align-middle">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="align-middle fw-bold">{{ $req->room->name }}</td>
                                            <td class="align-middle">
                                                <div class="fw-bold">{{ $req->title }}</div>
                                                <small class="text-muted">{{ Str::limit($req->description, 50) }}</small>
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $prioClass = match($req->priority) {
                                                        'high' => 'danger',
                                                        'medium' => 'warning',
                                                        'low' => 'info',
                                                        default => 'secondary'
                                                    };
                                                    $prioLabel = match($req->priority) {
                                                        'high' => 'Cao',
                                                        'medium' => 'Trung bình',
                                                        'low' => 'Thấp',
                                                        default => $req->priority
                                                    };
                                                @endphp
                                                <span class="badge badge-{{ $prioClass }}">{{ $prioLabel }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="badge badge-{{ $req->statusBadge() }}">{{ $req->statusLabel() }}</span>
                                            </td>
                                            <td class="text-right px-4 align-middle">
                                                <a href="{{ route('maintenance.show', $req) }}" class="btn btn-sm btn-outline-secondary">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">Bạn chưa có yêu cầu bảo trì nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
