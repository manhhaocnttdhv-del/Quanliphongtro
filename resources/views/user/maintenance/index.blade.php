@extends('layouts.user')
@section('title', 'Yêu Cầu Bảo Trì')

@section('styles')
<style>
.maintenance-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d6a4f 100%);
    padding: 60px 0 40px;
    color: #fff;
}
.maint-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}
.maint-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.1); }
.priority-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
.status-pill { border-radius: 50px; padding: 4px 14px; font-size: 12px; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="maintenance-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold mb-1">🔧 Yêu Cầu Bảo Trì</h1>
                <p class="mb-0 opacity-75">Theo dõi trạng thái các yêu cầu sửa chữa của bạn</p>
            </div>
            <a href="{{ route('maintenance.create') }}" class="btn btn-warning fw-semibold">
                <i class="fa fa-plus me-2"></i>Gửi yêu cầu mới
            </a>
        </div>
    </div>
</div>

<div class="container py-5">
    @forelse($requests as $req)
        <div class="maint-card card mb-3 p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        @php
                            $pColor = ['low'=>'#94a3b8','medium'=>'#3b82f6','high'=>'#f59e0b','urgent'=>'#ef4444'];
                        @endphp
                        <span class="priority-dot" style="background:{{ $pColor[$req->priority] ?? '#94a3b8' }};"></span>
                        <span class="text-muted small">{{ $req->priorityLabel() }}</span>
                        <span class="text-muted">•</span>
                        <span class="text-muted small">Phòng {{ $req->room->name }}</span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $req->title }}</h5>
                    <p class="text-muted mb-0 small">{{ Str::limit($req->description, 120) }}</p>
                    @if($req->admin_note)
                        <div class="mt-2 p-2 bg-light rounded" style="font-size:13px;">
                            <i class="fa fa-comment-o me-1 text-info"></i>
                            <strong>Phản hồi:</strong> {{ $req->admin_note }}
                        </div>
                    @endif
                </div>
                <div class="text-end ms-3">
                    @php
                        $statusClass = ['pending'=>'bg-warning text-dark','in_progress'=>'bg-info text-white','done'=>'bg-success text-white','rejected'=>'bg-danger text-white'];
                    @endphp
                    <span class="status-pill {{ $statusClass[$req->status] ?? 'bg-secondary text-white' }}">
                        {{ $req->statusLabel() }}
                    </span>
                    <div class="text-muted mt-2" style="font-size:12px;">{{ $req->created_at->format('d/m/Y') }}</div>
                    @if($req->resolved_at)
                        <div class="text-success" style="font-size:12px;"><i class="fa fa-check-circle me-1"></i>{{ $req->resolved_at->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
            @if($req->images && count($req->images))
                <div class="mt-3 pt-3 border-top d-flex gap-2 flex-wrap">
                    @foreach($req->images as $img)
                        <img src="{{ Storage::url($img) }}" class="rounded-2" style="width:70px;height:55px;object-fit:cover;" alt="">
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fa fa-wrench fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Chưa có yêu cầu bảo trì nào</h5>
            <p class="text-muted">Nếu phòng của bạn cần sửa chữa, hãy gửi yêu cầu ngay!</p>
            <a href="{{ route('maintenance.create') }}" class="btn btn-primary">Gửi yêu cầu đầu tiên</a>
        </div>
    @endforelse

    @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
