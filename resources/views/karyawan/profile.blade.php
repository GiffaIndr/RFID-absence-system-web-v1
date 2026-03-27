@extends('layouts.karyawan')
@section('title', 'Profil Saya')

@section('content')

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">

            {{-- Info Profil --}}
            <div class="card form-card mb-4 overflow-hidden">
                <div class="p-4 d-flex align-items-center gap-4"
                    style="background: linear-gradient(135deg, #0f172a, #1e3a5f)">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                        style="width:72px;height:72px;font-size:1.8rem;background:#10b981;flex-shrink:0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="text-white fw-semibold mb-1">{{ auth()->user()->name }}</h5>
                        <div class="text-white-50 small">{{ $employee?->position ?? '-' }} ·
                            {{ $employee?->department ?? '-' }}</div>
                        <div class="text-white-50 small mt-1">
                            <i class="bi bi-envelope me-1"></i>{{ auth()->user()->email }}
                        </div>
                    </div>
                    @if ($employee)
                        <div class="ms-auto text-end">
                            <div class="text-white-50 small">Kode Karyawan</div>
                            <div class="text-white fw-semibold font-monospace">{{ $employee->employee_code }}</div>
                            <div class="text-white-50 small mt-2">Bergabung</div>
                            <div class="text-white small">{{ $employee->join_date?->format('d M Y') ?? '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Form Edit --}}
            <div class="card form-card p-4">
                <h6 class="fw-semibold mb-4">Edit Profil</h6>
                <form action="{{ route('karyawan.profile.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" disabled>
                            <small class="text-muted">Nama hanya bisa diubah oleh HRD</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee?->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $employee?->address) }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-semibold mb-3">Ganti Password</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak ingin ganti">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Min. 8 karakter">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection
