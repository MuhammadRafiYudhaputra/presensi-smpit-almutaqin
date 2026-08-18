<?php

namespace App\Jobs;

use App\Models\Kehadiran;
use App\Services\FonnteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    protected int $kehadiranId;
    protected string $tipe;

    public function __construct(int $kehadiranId, string $tipe = 'masuk')
    {
        $this->kehadiranId = $kehadiranId;
        $this->tipe = $tipe;
    }

    public function handle(FonnteService $fonnteService): void
    {
        $kehadiran = Kehadiran::with(['siswa.orangTua', 'siswa.kelas'])->find($this->kehadiranId);

        if (!$kehadiran) {
            return;
        }

        $success = $fonnteService->sendPresensiNotification($kehadiran, $this->tipe);

        if ($success) {
            if ($this->tipe === 'masuk') {
                $kehadiran->update(['wa_masuk_sent' => true]);
            } else {
                $kehadiran->update(['wa_pulang_sent' => true]);
            }
        }
    }
}
