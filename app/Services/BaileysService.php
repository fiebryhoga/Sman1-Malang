<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaileysService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://127.0.0.1:3001';
    }

    public function sendMessage($number, $message, $deviceName = 'my-bot-session')
    {
        // Ganti nomor JID WhatsApp menjadi format yang dapat dibaca oleh Baileys.
        $number = str_replace(['@s.whatsapp.net', '@g.us'], '', $number);

        try {
            $response = Http::post("{$this->baseUrl}/send-message", [
                'deviceName' => $deviceName,
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('Pesan berhasil dikirim ke Baileys API.', ['target' => $number]);
                return true;
            }

            Log::error('Gagal mengirim pesan ke Baileys API.', [
                'target' => $number,
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Error saat koneksi ke Baileys API:', [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}