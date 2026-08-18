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
        $request->validate([
            'qr_code_token' => 'required|string',
        ]);

        $result = $this->presensiService->processScan($request->qr_code_token);

        return response()->json($result);
    }
}
