@extends('layouts.admin')
@section('title', 'Quản Lý Điện Nước')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 mb-1">Điện nước hàng tháng</h1>
    <a href="{{ route('admin.utilities.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Nhập chỉ số</a>
</div>

<div class="card mb-4 p-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <select class="form-select" name="room_id">
                <option value="">Tất cả phòng</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room_id')==$room->id?'selected':'' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Lọc</button></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Phòng</th><th>Tháng/Năm</th><th>Điện cũ→Mới</th><th>Tiền điện</th><th>Nước cũ→Mới</th><th>Tiền nước</th><th>Tổng</th></tr>
            </thead>
            <tbody>
                @forelse($utilities as $u)
                    <tr>
                        <td><strong>{{ $u->room->name }}</strong></td>
                        <td>{{ $u->month }}/{{ $u->year }}</td>
                        <td class="small">{{ $u->electricity_old }} → {{ $u->electricity_new }} <span class="text-muted">({{ $u->electricity_new - $u->electricity_old }} kWh)</span></td>
                        <td>{{ number_format($u->electricity_amount) }}đ</td>
                        <td class="small">{{ $u->water_old }} → {{ $u->water_new }} <span class="text-muted">({{ $u->water_new - $u->water_old }} m³)</span></td>
                        <td>{{ number_format($u->water_amount) }}đ</td>
                        <td><strong>{{ number_format($u->electricity_amount + $u->water_amount) }}đ</strong></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có dữ liệu điện nước</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $utilities->links() }}</div>
</div>
@endsection
