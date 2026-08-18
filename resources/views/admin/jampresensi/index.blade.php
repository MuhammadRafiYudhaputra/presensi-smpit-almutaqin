@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom p-4 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                    <i class="fa-solid fa-clock fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Pengaturan Jam Operasional Presensi</h5>
                    <small class="text-muted">Tentukan batas jam masuk, toleransi keterlambatan, dan jam kepulangan sekolah</small>
                </div>
            </div>

            <form action="{{ route('admin.jampresensi.update') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">Nama Jadwal Presensi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_jadwal" class="form-control" value="{{ old('nama_jadwal', $jamPresensi->nama_jadwal ?? 'Jadwal Reguler Sekolah') }}" required>
                    <small class="text-muted">Nama identitas jadwal operasional sekolah</small>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-success"><i class="fa-solid fa-bell me-1"></i> Jam Masuk</label>
                            <input type="time" name="jam_masuk" class="form-control form-control-lg text-center fw-bold" value="{{ old('jam_masuk', substr($jamPresensi->jam_masuk ?? '07:00:00', 0, 5)) }}" required>
                            <small class="text-muted d-block mt-2">Siswa hadir sebelum jam ini dihitung <strong>HADIR TEPAT WAKTU</strong>.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Jam Terlambat</label>
                            <input type="time" name="jam_terlambat" class="form-control form-control-lg text-center fw-bold" value="{{ old('jam_terlambat', substr($jamPresensi->jam_terlambat ?? '07:15:00', 0, 5)) }}" required>
                            <small class="text-muted d-block mt-2">Hadir setelah jam ini dihitung <strong>TERLAMBAT</strong>.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-primary"><i class="fa-solid fa-door-open me-1"></i> Jam Pulang</label>
                            <input type="time" name="jam_pulang" class="form-control form-control-lg text-center fw-bold" value="{{ old('jam_pulang', substr($jamPresensi->jam_pulang ?? '15:00:00', 0, 5)) }}" required>
                            <small class="text-muted d-block mt-2">Siswa baru diperbolehkan scan pulang setelah jam ini.</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-info d-flex align-items-center mb-4">
                    <i class="fa-solid fa-circle-info fs-4 me-3 text-info"></i>
                    <small class="text-dark">
                        Sistem secara otomatis menolak dan memberi peringatan jika siswa mencoba melakukan presensi pulang sebelum waktu <strong>Jam Pulang</strong> yang telah ditentukan di atas.
                    </small>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan Jam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
