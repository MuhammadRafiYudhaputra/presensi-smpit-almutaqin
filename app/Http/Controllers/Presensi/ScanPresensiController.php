<?php

namespace App\Http\Controllers\Presensi;

use App\Http\Controllers\Controller;
use App\Services\PresensiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScanPresensiController extends Controller
{
    protected PresensiService $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    /**
     * Tampilan Kios Scanner Presensi
     */
    public function index()
    {
        return view('presensi.scan');
    }

    /**
     * Endpoint API Ajax untuk pemrosesan Tap QR Code
     */
    public function store(Request $request): JsonResponse
    {
        $token = $request->input('qr_code_token') ?? $request->input('qr_token');

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token QR Code atau NISN tidak boleh kosong!',
            ], 400);
        }

        $result = $this->presensiService->processScan(trim($token));

        return response()->json($result);
    }
}
