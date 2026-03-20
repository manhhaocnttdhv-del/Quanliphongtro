<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Thông tin cá nhân</h2>
        <p class="mt-1 text-sm text-gray-600">Cập nhật thông tin tài khoản và địa chỉ liên lạc.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        {{-- Tên + Email --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" value="Họ và tên *" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="email" value="Email *" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                    :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="text-sm mt-2 text-red-600">
                        Email chưa xác minh.
                        <button form="send-verification" class="underline text-blue-600 hover:text-blue-800">
                            Gửi lại email xác minh
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-sm text-green-600">Đã gửi link xác minh!</p>
                    @endif
                @endif
            </div>
        </div>

        {{-- SĐT + CCCD --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" value="Số điện thoại" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                    :value="old('phone', $user->phone)" placeholder="0912 345 678" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="id_card" value="Số CCCD / CMND" />
                <x-text-input id="id_card" name="id_card" type="text" class="mt-1 block w-full"
                    :value="old('id_card', $user->id_card)" placeholder="0123456789" />
                <x-input-error class="mt-2" :messages="$errors->get('id_card')" />
            </div>
        </div>

        {{-- Ngày sinh + Giới tính --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="dob" value="Ngày sinh" />
                <x-text-input id="dob" name="dob" type="date" class="mt-1 block w-full"
                    :value="old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '')" />
                <x-input-error class="mt-2" :messages="$errors->get('dob')" />
            </div>
            <div>
                <x-input-label for="gender" value="Giới tính" />
                <select id="gender" name="gender"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Chọn --</option>
                    <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Khác</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>
        </div>

        {{-- Tỉnh / Huyện / Xã --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="p_province" value="Tỉnh / Thành phố" />
                <select id="p_province" name="province_name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Chọn tỉnh --</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('province_name')" />
            </div>
            <div>
                <x-input-label for="p_district" value="Quận / Huyện" />
                <select id="p_district" name="district_name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                    <option value="">-- Chọn quận --</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('district_name')" />
            </div>
            <div>
                <x-input-label for="p_ward" value="Phường / Xã" />
                <select id="p_ward" name="ward_name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                    <option value="">-- Chọn phường --</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('ward_name')" />
            </div>
        </div>

        {{-- Địa chỉ chi tiết --}}
        <div>
            <x-input-label for="address_detail" value="Địa chỉ chi tiết (số nhà, tên đường...)" />
            <x-text-input id="address_detail" name="address_detail" type="text" class="mt-1 block w-full"
                :value="old('address_detail', $user->address_detail)" placeholder="VD: Số 12, Đường Lê Lợi" />
            <x-input-error class="mt-2" :messages="$errors->get('address_detail')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>Lưu thay đổi</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-green-600 font-medium">✓ Đã lưu!</p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
(function() {
    const savedProvince = @json(old('province_name', $user->province_name ?? ''));
    const savedDistrict = @json(old('district_name', $user->district_name ?? ''));
    const savedWard     = @json(old('ward_name',     $user->ward_name     ?? ''));

    const pSel = document.getElementById('p_province');
    const dSel = document.getElementById('p_district');
    const wSel = document.getElementById('p_ward');

    function loadDistricts(provinceName, selectVal) {
        dSel.innerHTML = '<option value="">-- Chọn quận --</option>';
        wSel.innerHTML = '<option value="">-- Chọn phường --</option>';
        dSel.disabled = true; wSel.disabled = true;
        if (!provinceName) return;

        // Find province code from already-loaded options
        const opt = Array.from(pSel.options).find(o => o.value === provinceName);
        if (!opt) return;
        const code = opt.dataset.code;

        fetch('/api/regions/districts/' + code)
            .then(r => r.json())
            .then(data => {
                data.forEach(d => {
                    const o = new Option(d.name, d.name);
                    o.dataset.code = d.code;
                    if (d.name === selectVal) o.selected = true;
                    dSel.appendChild(o);
                });
                dSel.disabled = false;
                if (selectVal) loadWards(selectVal, savedWard);
            });
    }

    function loadWards(districtName, selectVal) {
        wSel.innerHTML = '<option value="">-- Chọn phường --</option>';
        wSel.disabled = true;
        if (!districtName) return;

        const opt = Array.from(dSel.options).find(o => o.value === districtName);
        if (!opt) return;
        const code = opt.dataset.code;

        fetch('/api/regions/wards/' + code)
            .then(r => r.json())
            .then(data => {
                data.forEach(w => {
                    const o = new Option(w.name, w.name);
                    if (w.name === selectVal) o.selected = true;
                    wSel.appendChild(o);
                });
                wSel.disabled = false;
            });
    }

    // Load provinces on page load
    fetch('/api/regions/provinces')
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                const o = new Option(p.name, p.name);
                o.dataset.code = p.code;
                if (p.name === savedProvince) o.selected = true;
                pSel.appendChild(o);
            });
            if (savedProvince) loadDistricts(savedProvince, savedDistrict);
        });

    pSel.addEventListener('change', () => loadDistricts(pSel.value, ''));
    dSel.addEventListener('change', () => loadWards(dSel.value, ''));
})();
</script>
@endpush
