<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrService
{
    private string $apiKey;
    private string $model = 'gemini-2.5-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function scanReceipt(string $imagePath): array
    {
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            Log::error('File tidak ditemukan: ' . $fullPath);
            return $this->fallbackResult('File tidak ditemukan.');
        }

        $imageData = base64_encode(file_get_contents($fullPath));
        $mimeType  = mime_content_type($fullPath);
        $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mimeType, $allowed)) {
            $mimeType = 'image/jpeg';
        }

        $prompt = <<<PROMPT
Kamu adalah asisten OCR struk belanja Indonesia. Baca gambar struk ini.

Balas HANYA dengan JSON valid. Mulai langsung dengan { dan akhiri dengan }. TANPA teks lain, TANPA markdown.

{
  "store_name": "nama toko atau null",
  "receipt_date": "YYYY-MM-DD atau null",
  "items": [
    {
      "item_name": "nama produk",
      "qty": 1,
      "unit_price": 10000,
      "subtotal": 10000
    }
  ],
  "subtotal": 50000,
  "tax": 0,
  "discount": 0,
  "total": 50000,
  "payment_method": "Cash/Debit/Kredit/QRIS atau null",
  "notes": null
}

ATURAN:
- Semua harga harus angka bulat integer TANPA titik/koma/Rp
- Baca SEMUA item di struk
- subtotal tiap item = qty x unit_price
- null untuk info yang tidak ada
PROMPT;

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(90)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data'      => $imageData,
                                ],
                            ],
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'     => 0.1,
                    'maxOutputTokens' => 4096,
                ],
            ]);

            Log::info('Gemini status: ' . $response->status());
            Log::info('Gemini body: ' . substr($response->body(), 0, 2000));

            if ($response->failed()) {
                Log::error('Gemini HTTP error: ' . $response->body());
                return $this->fallbackResult('API error ' . $response->status());
            }

            // Gemini 2.5 kadang return multiple parts — ambil semua text
            $candidates = $response->json('candidates') ?? [];
            $content    = '';

            foreach ($candidates as $candidate) {
                $parts = $candidate['content']['parts'] ?? [];
                foreach ($parts as $part) {
                    // Ambil hanya yang ada 'text', skip 'thought'
                    if (isset($part['text']) && !isset($part['thought'])) {
                        $content .= $part['text'];
                    }
                }
            }

            Log::info('Gemini raw content: ' . $content);

            if (empty($content)) {
                Log::error('Gemini content kosong');
                return $this->fallbackResult('Response kosong.');
            }

            // Bersihkan markdown
            $content = trim($content);
            $content = preg_replace('/^```json\s*/im', '', $content);
            $content = preg_replace('/^```\s*/im', '', $content);
            $content = preg_replace('/\s*```$/im', '', $content);
            $content = trim($content);

            // Ekstrak JSON — ambil dari { pertama sampai } terakhir
            $start = strpos($content, '{');
            $end   = strrpos($content, '}');
            if ($start !== false && $end !== false) {
                $content = substr($content, $start, $end - $start + 1);
            }

            Log::info('Gemini cleaned JSON: ' . $content);

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON decode error: ' . json_last_error_msg());
                return $this->fallbackResult('Format JSON tidak valid.');
            }

            // Pastikan items ada
            if (!isset($data['items']) || !is_array($data['items'])) {
                $data['items'] = [];
            }

            // Hitung ulang total jika kosong
            if (empty($data['total']) || $data['total'] == 0) {
                $data['total'] = collect($data['items'])->sum('subtotal');
            }

            // Bersihkan angka dari format string
            $data['total']    = $this->cleanNumber($data['total']    ?? 0);
            $data['subtotal'] = $this->cleanNumber($data['subtotal'] ?? 0);
            $data['tax']      = $this->cleanNumber($data['tax']      ?? 0);
            $data['discount'] = $this->cleanNumber($data['discount'] ?? 0);

            foreach ($data['items'] as &$item) {
                $item['unit_price'] = $this->cleanNumber($item['unit_price'] ?? 0);
                $item['subtotal']   = $this->cleanNumber($item['subtotal']   ?? 0);
                $item['qty']        = (int) ($item['qty'] ?? 1);
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('OcrService exception: ' . $e->getMessage());
            return $this->fallbackResult('Error: ' . $e->getMessage());
        }
    }

    private function cleanNumber(mixed $value): int
    {
        if (is_numeric($value)) return (int) $value;
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);
        return (int) $cleaned;
    }

    private function fallbackResult(string $msg = ''): array
    {
        return [
            'store_name'     => null,
            'receipt_date'   => null,
            'items'          => [],
            'subtotal'       => 0,
            'tax'            => 0,
            'discount'       => 0,
            'total'          => 0,
            'payment_method' => null,
            'notes'          => $msg ?: 'Gagal membaca struk. Silakan isi manual.',
        ];
    }
}