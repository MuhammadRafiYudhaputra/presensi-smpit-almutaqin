<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Tampilkan Halaman Backup & Restore
     */
    public function index()
    {
        $driver = config('database.default');
        $dbName = config("database.connections.{$driver}.database");
        
        // Hitung perkiraan ukuran database
        $dbSize = '0 KB';
        if ($driver === 'sqlite' && file_exists($dbName)) {
            $bytes = filesize($dbName);
            $dbSize = round($bytes / 1024, 2) . ' KB';
            if ($bytes >= 1048576) {
                $dbSize = round($bytes / 1048576, 2) . ' MB';
            }
        }

        // Hitung total file QR code
        $qrPath = public_path('images/qr');
        $totalQrFiles = 0;
        $qrFolderSize = '0 KB';
        if (File::exists($qrPath)) {
            $files = File::files($qrPath);
            $totalQrFiles = count($files);
            $totalBytes = 0;
            foreach ($files as $file) {
                $totalBytes += $file->getSize();
            }
            $qrFolderSize = round($totalBytes / 1024, 2) . ' KB';
            if ($totalBytes >= 1048576) {
                $qrFolderSize = round($totalBytes / 1048576, 2) . ' MB';
            }
        }

        return view('admin.backup.index', compact('driver', 'dbSize', 'totalQrFiles', 'qrFolderSize'));
    }

    /**
     * Unduh Backup Database (.SQL)
     */
    public function backupDatabase()
    {
        $driver = config('database.default');
        $timestamp = date('Y-m-d_His');
        $filename = "backup_database_smpit_almutaqin_{$timestamp}.sql";

        $sql = "-- ========================================================\n";
        $sql .= "-- BACKUP DATABASE SMP IT AL-MUTTAQIN\n";
        $sql .= "-- Waktu Backup : " . date('d-m-Y H:i:s') . "\n";
        $sql .= "-- Driver       : " . strtoupper($driver) . "\n";
        $sql .= "-- ========================================================\n\n";

        if ($driver === 'sqlite') {
            $sql .= "PRAGMA foreign_keys = OFF;\n\n";

            // Ambil seluruh nama tabel di SQLite
            $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->name;
                $createSql = $tableObj->sql;

                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Struktur Tabel `{$tableName}`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= "{$createSql};\n\n";

                // Ambil semua data baris
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data Tabel `{$tableName}`\n";
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(function ($c) {
                            return "`" . str_replace("`", "``", $c) . "`";
                        }, $columns);

                        $escapedValues = array_map(function ($val) {
                            if (is_null($val)) return "NULL";
                            return "'" . str_replace("'", "''", (string) $val) . "'";
                        }, array_values($rowArray));

                        $sql .= "INSERT INTO `{$tableName}` (" . implode(", ", $escapedColumns) . ") VALUES (" . implode(", ", $escapedValues) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "PRAGMA foreign_keys = ON;\n";

        } else {
            // MySQL Dump Generator
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\nSTART TRANSACTION;\n\n";

            $tables = DB::select('SHOW TABLES');
            $dbProp = "Tables_in_" . config("database.connections.mysql.database");

            foreach ($tables as $tableObj) {
                $tableName = isset($tableObj->$dbProp) ? $tableObj->$dbProp : array_values((array)$tableObj)[0];

                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "-- Struktur Tabel `{$tableName}`\n";
                $sql .= "-- --------------------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createTable[0]->{'Create Table'};
                $sql .= "{$createSql};\n\n";

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Data Tabel `{$tableName}`\n";
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(function ($c) {
                            return "`" . str_replace("`", "``", $c) . "`";
                        }, $columns);

                        $escapedValues = array_map(function ($val) {
                            if (is_null($val)) return "NULL";
                            return "'" . addslashes((string) $val) . "'";
                        }, array_values($rowArray));

                        $sql .= "INSERT INTO `{$tableName}` (" . implode(", ", $escapedColumns) . ") VALUES (" . implode(", ", $escapedValues) . ");\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\nCOMMIT;\n";
        }

        return response($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Restore Database dari File SQL (.SQL)
     */
    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|max:51200' // Maks 50MB
        ], [
            'sql_file.required' => 'Silakan pilih file backup database terlebih dahulu.',
            'sql_file.file' => 'Berkas yang diunggah tidak valid.',
            'sql_file.max' => 'Ukuran file cadangan maksimal 50 MB.'
        ]);

        $file = $request->file('sql_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['sql', 'txt'])) {
            return back()->with('error', 'Format file harus berupa .sql atau .txt');
        }

        try {
            $sqlContent = file_get_contents($file->getRealPath());

            if (empty(trim($sqlContent))) {
                return back()->with('error', 'File SQL kosong atau tidak memiliki kueri yang valid.');
            }

            $driver = config('database.default');

            if ($driver === 'sqlite') {
                DB::connection()->getPdo()->exec("PRAGMA foreign_keys = OFF;");
                DB::connection()->getPdo()->exec($sqlContent);
                DB::connection()->getPdo()->exec("PRAGMA foreign_keys = ON;");
            } else {
                DB::connection()->getPdo()->exec("SET FOREIGN_KEY_CHECKS=0;");
                DB::connection()->getPdo()->exec($sqlContent);
                DB::connection()->getPdo()->exec("SET FOREIGN_KEY_CHECKS=1;");
            }

            return back()->with('success', 'Database berhasil dipulihkan (restore) dengan sukses! Seluruh data telah diperbarui.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Unduh Backup Foto & QR Code (.ZIP)
     */
    public function backupStorage()
    {
        $timestamp = date('Y-m-d_His');
        $zipFilename = "backup_foto_qr_code_{$timestamp}.zip";
        $tempPath = storage_path("app/{$zipFilename}");

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat arsip zip untuk backup foto.');
        }

        // 1. Masukkan file QR Code Siswa
        $qrPath = public_path('images/qr');
        if (File::exists($qrPath)) {
            $files = File::allFiles($qrPath);
            foreach ($files as $file) {
                $relativePath = 'qr/' . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        // 2. Masukkan file uploads lain jika ada
        $uploadsPath = public_path('images');
        if (File::exists($uploadsPath)) {
            $logoFile = public_path('images/logo.png');
            if (File::exists($logoFile)) {
                $zip->addFile($logoFile, 'logo.png');
            }
        }

        $zip->close();

        if (!file_exists($tempPath)) {
            return back()->with('error', 'Gagal memproses file arsip zip.');
        }

        return response()->download($tempPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Restore Foto & QR Code dari File Zip (.ZIP)
     */
    public function restoreStorage(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:102400' // Maks 100MB
        ], [
            'zip_file.required' => 'Silakan pilih file arsip .zip terlebih dahulu.',
            'zip_file.mimes' => 'Format berkas harus berupa arsip ZIP (.zip).',
            'zip_file.max' => 'Ukuran file ZIP maksimal 100 MB.'
        ]);

        $file = $request->file('zip_file');

        try {
            $zip = new ZipArchive();
            if ($zip->open($file->getRealPath()) === true) {
                $targetDir = public_path('images/qr');
                if (!File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                // Ekstrak file satu per satu dengan aman
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    // Cegah path traversal
                    if (str_contains($entryName, '..')) continue;

                    if (str_starts_with($entryName, 'qr/')) {
                        $cleanName = substr($entryName, 3);
                        if (!empty($cleanName)) {
                            $extractedContent = $zip->getFromIndex($i);
                            File::put(public_path('images/qr/' . $cleanName), $extractedContent);
                        }
                    } else {
                        $extractedContent = $zip->getFromIndex($i);
                        File::put(public_path('images/qr/' . basename($entryName)), $extractedContent);
                    }
                }

                $zip->close();
                return back()->with('success', 'File foto & QR Code berhasil dipulihkan (restore) ke sistem!');
            } else {
                return back()->with('error', 'Gagal membuka atau membaca berkas ZIP.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal melakukan restore foto/QR: ' . $e->getMessage());
        }
    }

    /**
     * Muat Ulang / Sinkronisasi Dataset 112 Siswa Dummy (Local & Railway)
     */
    public function seedDummy(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'DummySchoolDataSeeder',
                '--force' => true,
            ]);

            return back()->with('success', 'Dataset 112 Siswa (Kelas 7, 8, 9A, 9B), Riwayat Akademik, dan Sample Kehadiran berhasil disinkronisasi & dimuat ulang ke database!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat dataset: ' . $e->getMessage());
        }
    }
}
