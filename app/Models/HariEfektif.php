<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HariEfektif extends Model
{
    use HasFactory;

    protected $table = 'hari_efektifs';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'mode',
        'bulan',
        'tahun',
        'kelas_id',
        'jumlah_hari',
        'keterangan',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Dapatkan jumlah hari efektif per kelas atau fallback ke default kalender
     */
    public static function getForKelas(string $mode, string $tahunAjaran, string $semester, ?int $bulan, int $tahun, ?int $kelasId, ?string $namaKelas = null): int
    {
        try {
            // 1. Cek di database apakah ada entri custom untuk kelas_id ini
            if ($kelasId) {
                $query = static::where('mode', $mode)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->where('tahun', $tahun)
                    ->where('kelas_id', $kelasId);

                if ($mode === 'bulanan' && $bulan) {
                    $query->where('bulan', $bulan);
                }

                $record = $query->first();
                if ($record && $record->jumlah_hari > 0) {
                    return (int) $record->jumlah_hari;
                }
            }

            // 2. Cek apakah ada entri general (kelas_id null)
            $queryGeneral = static::where('mode', $mode)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->where('tahun', $tahun)
                ->whereNull('kelas_id');

            if ($mode === 'bulanan' && $bulan) {
                $queryGeneral->where('bulan', $bulan);
            }

            $generalRecord = $queryGeneral->first();
            if ($generalRecord && $generalRecord->jumlah_hari > 0) {
                // Jika ada namaKelas 9 dan semester genap, terapkan penyesuaian proporsional jika belum di-custom
                if ($namaKelas && str_contains(strtoupper($namaKelas), '9') && $semester === 'genap' && $mode === 'semester') {
                    return min($generalRecord->jumlah_hari, 85);
                }
                return (int) $generalRecord->jumlah_hari;
            }
        } catch (\Throwable $e) {
            // Jika tabel belum termigrasi di server hosting/Railway, fallback mulus tanpa error
        }

        // 3. Fallback: Hitung otomatis berdasarkan kalender kerja (Senin - Jumat non-libur nasional)
        return static::calculateDefaultCalendar($mode, $bulan, $tahun, $semester, $namaKelas);
    }

    /**
     * Hitung default hari kerja kalender
     */
    public static function calculateDefaultCalendar(string $mode, ?int $bulan, int $tahun, string $semester, ?string $namaKelas = null): int
    {
        if ($mode === 'bulanan' && $bulan) {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1);
            $endDate = $startDate->copy()->endOfMonth();
            $effectiveDays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!\App\Helpers\HolidayHelper::isNonEffectiveDay($current)) {
                    $effectiveDays++;
                }
                $current->addDay();
            }
            return max(1, $effectiveDays);
        } elseif ($mode === 'semester') {
            // Khusus Kelas 9 Semester Genap, hari belajar lebih pendek (~85 hari)
            if ($namaKelas && str_contains(strtoupper($namaKelas), '9') && $semester === 'genap') {
                return 85;
            }

            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;
            $startDate = Carbon::createFromDate($tahun, $startMonth, 1);
            $endDate = Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();
            $effectiveDays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!\App\Helpers\HolidayHelper::isNonEffectiveDay($current)) {
                    $effectiveDays++;
                }
                $current->addDay();
            }
            return max(1, $effectiveDays);
        }

        return 20;
    }
}
