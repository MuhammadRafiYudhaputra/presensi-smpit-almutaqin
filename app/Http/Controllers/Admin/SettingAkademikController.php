<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingAkademik;
use Illuminate\Http\Request;

class SettingAkademikController extends Controller
{
    /**
     * Update Semester & Tahun Ajaran Aktif
     */
    public function update(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
        ]);

        $setting = SettingAkademik::getActive();
        $setting->update([
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'is_active' => true,
        ]);

        $semesterLabel = ucfirst($request->semester);
        return redirect()->back()->with('success', "Periode Akademik Aktif berhasil diubah menjadi: Semester {$semesterLabel} Tahun Ajaran {$request->tahun_ajaran}!");
    }

    /**
     * One-click toggle switch between Semester Ganjil and Genap
     */
    public function toggleSemester(Request $request)
    {
        $setting = SettingAkademik::getActive();
        $newSemester = ($setting->semester === 'ganjil') ? 'genap' : 'ganjil';

        $setting->update([
            'semester' => $newSemester,
        ]);

        $semesterLabel = ucfirst($newSemester);
        return redirect()->back()->with('success', "Semester aktif berhasil dialihkan ke: Semester {$semesterLabel} ({$setting->tahun_ajaran})!");
    }
}
