@extends('layouts.user')
@section('title', 'Danh Sách Phòng - ' . \App\Models\Setting::get('site_name','Nhà Trọ'))

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css"/>
<style>
    /* Custom cluster icons */
    .custom-cluster {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: 3px solid #fff;
        border-radius: 50%;
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(217,119,6,0.4);
    }
    .custom-cluster-sm  { width: 34px; height: 34px; font-size: 12px; }
    .custom-cluster-md  { width: 42px; height: 42px; font-size: 13px; }
    .custom-cluster-lg  { width: 52px; height: 52px; font-size: 15px; }
    /* Popup */
    .map-card { width: 190px; }
    .map-card img { width:100%;height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px; }
    .map-card .mc-name { font-weight:700;font-size:13px;color:#1e293b;line-height:1.3;margin-bottom:4px; }
    .map-card .mc-price { color:#d97706;font-weight:700;font-size:13px;margin-bottom:4px; }
    .map-card .mc-addr { color:#64748b;font-size:11px;margin-bottom:8px;line-height:1.4; }
    .map-card .mc-btn {
        display:block;text-align:center;background:linear-gradient(135deg,#1e293b,#334155);
        color:#fff;text-decoration:none;padding:7px;border-radius:8px;font-size:12px;font-weight:600;
    }
    .map-card .mc-btn:hover { background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff; }
</style>
<style>
    .rooms-section { padding: 60px 0 80px; background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%); min-height: 80vh; }

    /* ===== SEARCH BAR ===== */
    .search-bar {
        background: #fff;
        padding: 28px 32px;
        border-radius: 18px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        margin-bottom: 32px;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .search-bar .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .search-bar .form-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
    .search-bar .form-control,
    .search-bar .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px 10px 36px;
        font-size: 13px;
        background-color: #f8fafc;
        transition: all 0.2s;
    }
    .search-bar .form-control:focus,
    .search-bar .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
        background-color: #fff;
    }
    .search-bar .form-select:disabled { background-color: #f1f5f9; color: #94a3b8; }
    .field-icon { position: relative; }
    .field-icon .fi {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); font-size: 13px;
        pointer-events: none; z-index: 5;
    }
    .btn-search {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        transition: all 0.2s;
    }
    .btn-search:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(245,158,11,0.4); color: #fff; }

    /* ===== RESULT HEADER ===== */
    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .result-count {
        font-size: 14px;
        color: #64748b;
    }
    .result-count strong { color: #1e293b; font-size: 18px; }
    .view-toggle .btn {
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
    }

    /* ===== ROOM CARDS ===== */
    .room-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .room-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.1);
    }
    .room-card .image-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
    }
    .room-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .room-card:hover img { transform: scale(1.08); }

    .room-card .price-tag {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        padding: 5px 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        color: #fff;
        box-shadow: 0 4px 12px rgba(217,119,6,0.3);
    }
    .room-card .status-pill {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
    }
    .room-card-body { padding: 18px 20px; }
    .room-card-body .room-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
        min-height: 42px;
    }
    .room-card-body .room-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }
    .room-card-body .room-title a:hover { color: #d97706; }
    .room-card .room-meta {
        display: flex;
        gap: 14px;
        font-size: 12px;
        color: #64748b;
        margin-bottom: 12px;
    }
    .room-card .room-meta i { color: #f59e0b; margin-right: 4px; width: 14px; text-align: center; }
    .room-card .room-location {
        font-size: 11px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }
    .room-card .room-location i { color: #ef4444; font-size: 12px; }
    .room-card .btn-detail {
        display: block;
        text-align: center;
        padding: 10px;
        background: #1e293b;
        color: #fff;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 14px;
        transition: all 0.2s;
    }
    .room-card .btn-detail:hover { background: #d97706; color: #fff; transform: translateY(-1px); }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .empty-state i { font-size: 60px; color: #e2e8f0; margin-bottom: 20px; }
    .empty-state h4 { color: #475569; font-weight: 700; margin-bottom: 8px; }
    .empty-state p { color: #94a3b8; margin-bottom: 20px; }

    /* ===== MAP ===== */
    #listingMap {
        height: 600px;
        border-radius: 16px;
        border: 1.5px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .map-popup-img {
        width: 100%; height: 100px; object-fit: cover;
        border-radius: 6px; margin-bottom: 8px;
    }
    .map-popup-name { font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    .map-popup-price { color: #f59e0b; font-weight: 600; font-size: 12px; margin-bottom: 4px; }
    .map-popup-addr { color: #64748b; font-size: 11px; margin-bottom: 8px; }
    .map-popup-link {
        display: block; text-align: center; background: #1e293b;
        color: #fff; text-decoration: none; padding: 6px; border-radius: 6px;
        font-size: 11px; font-weight: 600;
    }
    .map-popup-link:hover { background: #fbbf24; color: #000; }
    .no-location-note {
        background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px;
        padding: 12px 16px; font-size: 13px; color: #92400e; margin-top: 12px;
    }

    /* ===== PAGINATION ===== */
    .pagination { gap: 4px; }
    .page-link {
        border-radius: 8px !important;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 14px;
        transition: all 0.2s;
    }
    .page-link:hover { background: #f59e0b; border-color: #f59e0b; color: #fff; }
    .page-item.active .page-link { background: #f59e0b; border-color: #f59e0b; color: #fff; }
</style>
@endsection

@section('content')
<div class="rooms-section">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2" style="color: #1e293b;">Tìm kiếm phòng trọ</h2>
            <p class="text-muted mb-0">Dễ dàng tìm kiếm phòng trọ phù hợp với nhu cầu của bạn</p>
        </div>

        {{-- Search bar --}}
        <div class="search-bar">
            <form method="GET" action="{{ route('rooms.index') }}">
                <div class="row g-4 align-items-end">
                    <div class="col">
                        <label class="form-label">Từ khóa</label>
                        <div class="field-icon">
                            <i class="fi fa fa-search text-muted"></i>
                            <input type="text" class="form-control" name="search"
                                   value="{{ request('search') }}" placeholder="Tên phòng, mô tả...">
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Tỉnh / TP</label>
                        <div class="field-icon">
                            <i class="fi fa fa-map-marker" style="color:#ef4444;"></i>
                            <select name="province" id="province" class="form-select">
                                <option value="">-- Tất cả --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Quận / Huyện</label>
                        <div class="field-icon">
                            <i class="fi fa fa-building" style="color:#3b82f6;"></i>
                            <select name="district" id="district" class="form-select" disabled>
                                <option value="">-- Chọn quận --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Phường / Xã</label>
                        <div class="field-icon">
                            <i class="fi fa fa-home" style="color:#22c55e;"></i>
                            <select name="ward" id="ward" class="form-select" disabled>
                                <option value="">-- Chọn phường --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Giá tối đa (đ)</label>
                        <div class="field-icon">
                            <i class="fi fa fa-money" style="color:#f59e0b;"></i>
                            <input type="number" class="form-control" name="max_price"
                                   value="{{ request('max_price') }}" placeholder="3,000,000">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-search" style="padding:11px 20px;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- View Toggle --}}
        <div class="result-header">
            <div class="result-count">
                Tìm thấy <strong>{{ $rooms->total() }}</strong> phòng trọ
            </div>
            <div class="view-toggle btn-group" role="group">
                <button id="btnList" type="button" class="btn btn-warning text-dark" onclick="switchView('list')">
                    <i class="fa fa-th-large me-1"></i> Dạng thẻ
                </button>
                <button id="btnMap" type="button" class="btn btn-outline-secondary" onclick="switchView('map')">
                    <i class="fa fa-map-o me-1"></i> Bản đồ
                </button>
            </div>
        </div>

        {{-- DẠNG THẺ --}}
        <div id="listView">
            <div class="row g-4">
                @forelse($rooms as $room)
                    <div class="col-lg-4 col-md-6">
                        <div class="room-card h-100">
                            <div class="image-wrapper">
                                <a href="{{ route('rooms.show', $room) }}">
                                    @if($room->images->first())
                                        <img src="{{ asset('storage/'.$room->images->first()->image_path) }}" alt="{{ $room->name }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&h=400&fit=crop" alt="{{ $room->name }}">
                                    @endif
                                </a>
                                <div class="price-tag">{{ number_format($room->price) }}đ/tháng</div>
                                <span class="status-pill bg-{{ $room->statusBadge() === 'success' ? 'success' : 'secondary' }} text-white">
                                    {{ $room->statusLabel() }}
                                </span>
                            </div>
                            <div class="room-card-body">
                                <div class="room-title">
                                    <a href="{{ route('rooms.show', $room) }}">{{ $room->name }}</a>
                                </div>
                                <div class="room-meta">
                                    @if($room->area)
                                        <span><i class="fa fa-expand"></i>{{ $room->area }} m²</span>
                                    @endif
                                    @if($room->floor)
                                        <span><i class="fa fa-building"></i>Tầng {{ $room->floor }}</span>
                                    @endif
                                    @if(is_array($room->amenities) && count($room->amenities) > 0)
                                        <span><i class="fa fa-check-circle"></i>{{ $room->amenities[0] }}</span>
                                    @endif
                                </div>
                                <div class="room-location">
                                    <i class="fa fa-map-marker"></i>
                                    <span>{{ $room->district_name ?? '' }}{{ $room->district_name && $room->province_name ? ', ' : '' }}{{ $room->province_name ?? '' }}</span>
                                </div>
                                <a href="{{ route('rooms.show', $room) }}" class="btn-detail">
                                    Xem chi tiết <i class="fa fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fa fa-search"></i>
                            <h4>Không tìm thấy kết quả</h4>
                            <p>Hãy thử thay đổi từ khóa hoặc khoảng giá nhé.</p>
                            <a href="{{ route('rooms.index', ['province' => '']) }}" class="btn btn-warning px-4 rounded-pill fw-bold text-dark">Xem tất cả phòng</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $rooms->links() }}
            </div>
        </div>

        {{-- DẠNG BẢN ĐỒ --}}
        <div id="mapView" style="display:none;">
            <div id="listingMap"></div>

            @php
                $roomsWithLocation = $rooms->filter(fn($r) => $r->hasLocation());
                $roomsNoLocation   = $rooms->filter(fn($r) => !$r->hasLocation());
            @endphp

            @if($roomsNoLocation->count() > 0)
                <div class="no-location-note mt-3">
                    <i class="fa fa-info-circle me-1"></i>
                    <strong>{{ $roomsNoLocation->count() }} phòng</strong> chưa được gắn vị trí bản đồ.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
// ===== VIEW TOGGLE =====
let mapInitialized = false;

function switchView(mode) {
    if (mode === 'map') {
        document.getElementById('listView').style.display = 'none';
        document.getElementById('mapView').style.display  = 'block';
        document.getElementById('btnList').classList.remove('btn-warning', 'text-dark');
        document.getElementById('btnList').classList.add('btn-outline-secondary');
        document.getElementById('btnMap').classList.remove('btn-outline-secondary');
        document.getElementById('btnMap').classList.add('btn-warning', 'text-dark');
        if (!mapInitialized) {
            initMap();
            mapInitialized = true;
        }
    } else {
        document.getElementById('listView').style.display = 'block';
        document.getElementById('mapView').style.display  = 'none';
        document.getElementById('btnList').classList.add('btn-warning', 'text-dark');
        document.getElementById('btnList').classList.remove('btn-outline-secondary');
        document.getElementById('btnMap').classList.add('btn-outline-secondary');
        document.getElementById('btnMap').classList.remove('btn-warning', 'text-dark');
    }
}

// ===== ROOM MAP via API =====
// Pass current URL filters to the map API
const mapApiUrl = '/api/rooms/map?' + window.location.search.replace(/^\?/, '');

function renderMapPins(map, roomsData) {
    const bounds = [];

    // Marker cluster group with custom icon
    const cluster = L.markerClusterGroup({
        maxClusterRadius: 50,
        iconCreateFunction: function(c) {
            const count = c.getChildCount();
            let cls = count < 10 ? 'sm' : count < 50 ? 'md' : 'lg';
            return L.divIcon({
                html: `<div class="custom-cluster custom-cluster-${cls}">${count}</div>`,
                className: '', iconSize: null
            });
        }
    });

    roomsData.forEach(function(room) {
        const isAvail = room.badge === 'success';
        const grad    = isAvail ? ['#22c55e','#16a34a'] : ['#ef4444','#b91c1c'];

        // Beautiful teardrop SVG pin
        const svgPin = `
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 32 42">
              <defs>
                <linearGradient id="g${room.id}" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="${grad[0]}"/>
                  <stop offset="100%" stop-color="${grad[1]}"/>
                </linearGradient>
              </defs>
              <path d="M16 0 C7.2 0 0 7.2 0 16 C0 27 16 42 16 42 C16 42 32 27 32 16 C32 7.2 24.8 0 16 0Z"
                    fill="url(#g${room.id})" stroke="white" stroke-width="2"/>
              <text x="16" y="22" text-anchor="middle" fill="white" font-size="14" font-family="FontAwesome">&#xf015;</text>
            </svg>`;

        const pinIcon = L.divIcon({
            html: svgPin,
            iconSize: [32, 42],
            iconAnchor: [16, 42],
            popupAnchor: [0, -44],
            className: ''
        });

        // Truncate address if it's too long
        const addr = room.address
            ? (room.address.length > 80 ? room.address.substring(0, 80) + '...' : room.address)
            : '';

        const imgTag = room.img
            ? `<img src="${room.img}" alt="${room.name}">`
            : '';

        const popup = `
            <div class="map-card">
                ${imgTag}
                <div class="mc-name">${room.name}</div>
                <div class="mc-price">&#x1F4B0; ${room.price}</div>
                ${addr ? `<div class="mc-addr">&#x1F4CD; ${addr}</div>` : ''}
                <a class="mc-btn" href="${room.url}">Xem chi ti&#x1EBF;t &#x2192;</a>
            </div>`;

        const marker = L.marker([room.lat, room.lng], { icon: pinIcon })
            .bindPopup(popup, { maxWidth: 210, className: 'room-popup' });

        cluster.addLayer(marker);
        bounds.push([room.lat, room.lng]);
    });

    map.addLayer(cluster);

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [50, 50] });
    } else if (bounds.length === 1) {
        map.setView(bounds[0], 16);
    }
}

function initMap() {
    const map = L.map('listingMap').setView([18.6796, 105.6813], 12);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Loading indicator
    document.getElementById('listingMap').insertAdjacentHTML('beforeend',
        '<div id="mapLoading" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:999;background:rgba(255,255,255,.9);padding:12px 24px;border-radius:10px;font-weight:600;font-size:13px;color:#475569;"><i class=\"fa fa-spinner fa-spin me-2\"></i>Đang tải bản đồ...</div>'
    );

    fetch(mapApiUrl)
        .then(r => r.json())
        .then(data => {
            document.getElementById('mapLoading')?.remove();
            if (data.length === 0) {
                map.setView([18.6796, 105.6813], 12);
                return;
            }
            renderMapPins(map, data);

            // Update count badge
            const countBadge = document.getElementById('mapRoomCount');
            if (countBadge) countBadge.textContent = data.length + ' phòng trên bản đồ';
        })
        .catch(() => {
            document.getElementById('mapLoading')?.remove();
        });
}


// ===== PROVINCE / DISTRICT / WARD CASCADING =====
$(document).ready(function() {
    const provinceSelect = $('#province');
    const districtSelect = $('#district');
    const wardSelect     = $('#ward');

    const currentProvince = @json(request('province', 'Tỉnh Nghệ An'));
    const currentDistrict = @json(request('district', ''));
    const currentWard     = @json(request('ward', ''));

    // Load provinces
    fetch('/api/regions/provinces')
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                const selected = (p.name === currentProvince) ? 'selected' : '';
                provinceSelect.append(`<option value="${p.name}" data-code="${p.code}" ${selected}>${p.name}</option>`);
            });

            // Auto-load districts if province is selected
            if (currentProvince) {
                const selectedOpt = provinceSelect.find(':selected');
                const code = selectedOpt.attr('data-code');
                if (code) loadDistricts(code, currentDistrict);
            }
        });

    function loadDistricts(code, selectValue) {
        districtSelect.empty().append('<option value="">-- Tất cả quận --</option>').prop('disabled', true);
        wardSelect.empty().append('<option value="">-- Chọn phường --</option>').prop('disabled', true);
        if (!code) return;

        fetch(`/api/regions/districts/${code}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(d => {
                    const sel = (d.name === selectValue) ? 'selected' : '';
                    districtSelect.append(`<option value="${d.name}" data-code="${d.code}" ${sel}>${d.name}</option>`);
                });
                districtSelect.prop('disabled', false);

                // Auto-load wards if district is selected
                if (selectValue) {
                    const selOpt = districtSelect.find(':selected');
                    const wCode = selOpt.attr('data-code');
                    if (wCode) loadWards(wCode, currentWard);
                }
            });
    }

    function loadWards(code, selectValue) {
        wardSelect.empty().append('<option value="">-- Tất cả phường --</option>').prop('disabled', true);
        if (!code) return;

        fetch(`/api/regions/wards/${code}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(w => {
                    const sel = (w.name === selectValue) ? 'selected' : '';
                    wardSelect.append(`<option value="${w.name}" ${sel}>${w.name}</option>`);
                });
                wardSelect.prop('disabled', false);
            });
    }

    provinceSelect.on('change', function() {
        const code = $(this).find(':selected').attr('data-code');
        loadDistricts(code, '');
    });

    districtSelect.on('change', function() {
        const code = $(this).find(':selected').attr('data-code');
        loadWards(code, '');
    });
});
</script>
@endsection
