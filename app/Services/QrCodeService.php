<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate unique QR Token for student
     */
    public function generateToken(string $nisn): string
    {
        return 'SMPIT-' . $nisn . '-' . Str::random(6);
    }

    /**
     * Render SVG QR Code string for Blade display
     */
    public function renderSvg(string $qrToken, int $size = 150): string
    {
        return QrCode::size($size)->generate($qrToken);
    }
}
