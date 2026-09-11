<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTua;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'ayah_asc');

        $query = OrangTua::with(['siswas.kelas']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ayah', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('nama_wali', 'like', "%{$search}%")
                  ->orWhere('hubungan_wali', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'ayah_desc':
                $query->orderBy('nama_ayah', 'desc');
                break;
            case 'ibu_asc':
                $query->orderBy('nama_ibu', 'asc');
                break;
            case 'wali_asc':
                $query->orderBy('nama_wali', 'asc');
                break;
            case 'no_wa':
                $query->orderBy('no_wa', 'asc');
                break;
            case 'ayah_asc':
            default:
                $query->orderBy('nama_ayah', 'asc');
                break;
        }

        $orangTuas = $query->paginate(15)->withQueryString();

        return view('admin.orangtua.index', compact('orangTuas', 'search', 'sortBy'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file_orangtua')) {
            return $this->importOrangTua($request);
        }

        $request->validate([
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'hubungan_wali' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        OrangTua::create($request->all());

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $orangTua = OrangTua::findOrFail($id);

        $request->validate([
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'hubungan_wali' => 'nullable|string|max:255',
            'no_wa' => 'required|string|max:30',
            'alamat' => 'nullable|string',
        ]);

        $orangTua->update($request->all());

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $orangTua = OrangTua::findOrFail($id);
        $orangTua->delete();

        return redirect()->back()->with('success', 'Data Orang Tua / Wali berhasil dihapus!');
    }

    /**
     * Import Data Orang Tua dari CSV atau Excel
     */
    public function importOrangTua(Request $request)
    {
        $request->validate([
            'file_orangtua' => 'required|file|max:10240',
        ], [
            'file_orangtua.required' => 'Silakan pilih file data orang tua terlebih dahulu.',
        ]);

        $file = $request->file('file_orangtua');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($ext, ['xlsx', 'xls']) && class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $rows = $spreadsheet->getActiveSheet()->toArray();
            } else {
                $content = file_get_contents($file->getRealPath());
                if (substr($content, 0, 3) === "\xEF\xBB\xBF") $content = substr($content, 3);
                $lines = preg_split('/\r\n|\r|\n/', trim($content));
                $delim = str_contains($lines[0] ?? '', ';') ? ';' : ',';
                foreach ($lines as $l) {
                    if (trim($l) !== '') $rows[] = str_getcsv($l, $delim);
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }

        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'File tidak memiliki baris data orang tua yang valid.');
        }

        $headers = array_shift($rows);
        $headerMap = [];
        foreach ($headers as $idx => $h) {
            $clean = strtolower(str_replace([' ', '_', '-'], '', trim((string)$h)));
            if (in_array($clean, ['namaayah', 'ayah'])) $headerMap['ayah'] = $idx;
            elseif (in_array($clean, ['namaibu', 'ibu'])) $headerMap['ibu'] = $idx;
            elseif (in_array($clean, ['namawali', 'wali'])) $headerMap['wali'] = $idx;
            elseif (in_array($clean, ['hubunganwali', 'hubungan'])) $headerMap['hubungan'] = $idx;
            elseif (in_array($clean, ['nowa', 'nohp', 'hp', 'telepon', 'whatsapp'])) $headerMap['wa'] = $idx;
            elseif (in_array($clean, ['alamat'])) $headerMap['alamat'] = $idx;
        }

        $imported = 0;
        foreach ($rows as $row) {
            $rawWa = isset($headerMap['wa']) ? trim((string)($row[$headerMap['wa']] ?? '')) : '';
            $noWa = preg_replace('/[^0-9]/', '', $rawWa);
            if (str_starts_with($noWa, '62')) $noWa = '0' . substr($noWa, 2);
            elseif (str_starts_with($noWa, '8')) $noWa = '0' . $noWa;

            if (empty($noWa)) continue;

            $ayah = isset($headerMap['ayah']) ? trim((string)($row[$headerMap['ayah']] ?? '')) : null;
            $ibu = isset($headerMap['ibu']) ? trim((string)($row[$headerMap['ibu']] ?? '')) : null;
            $wali = isset($headerMap['wali']) ? trim((string)($row[$headerMap['wali']] ?? '')) : null;
            $hubungan = isset($headerMap['hubungan']) ? trim((string)($row[$headerMap['hubungan']] ?? '')) : ($wali ? 'Wali' : 'Orang Tua Kandung');
            $alamat = isset($headerMap['alamat']) ? trim((string)($row[$headerMap['alamat']] ?? '')) : null;

            OrangTua::updateOrCreate(
                ['no_wa' => $noWa],
                [
                    'nama_ayah' => $ayah,
                    'nama_ibu' => $ibu,
                    'nama_wali' => $wali,
                    'hubungan_wali' => $hubungan,
                    'alamat' => $alamat
                ]
            );

            $imported++;
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data kontak Orang Tua / Wali.");
    }

    /**
     * Download template CSV Orang Tua
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_orang_tua.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['Nama Ayah', 'Nama Ibu', 'Nama Wali', 'Hubungan Wali', 'No WhatsApp', 'Alamat'];
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            fputcsv($file, ['Bapak Rahmat', 'Ibu Rahmawati', '', 'Orang Tua Kandung', '081234567890', 'Tasikmalaya']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
