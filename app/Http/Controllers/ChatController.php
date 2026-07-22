<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|in:user,assistant,system',
            'messages.*.content' => 'required|string|max:4000',
        ]);

        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'Maaf, konfigurasi AI belum tersedia. Hubungi administrator.'
            ], 500);
        }

        $systemPrompt = [
            'role' => 'system',
            'content' => 'Kamu adalah Asisten Sekolah SMK YAPIM BIRU-BIRU yang ramah dan pintar. '
                . 'Tugas utamamu adalah menjawab pertanyaan seputar kegiatan sekolah, akademik, dan administrasi. '
                . 'Namun, JIKA siswa bertanya tentang materi pelajaran (seperti IT, subnetting, matematika, dll) atau pertanyaan umum lainnya, JAWABLAH dengan lengkap, edukatif, dan jelas layaknya seorang guru. '
                . 'Jangan menolak menjawab pertanyaan pelajaran. Gunakan format markdown (tabel, list, bold) jika diperlukan agar rapi.'
        ];

        $messages = array_merge([$systemPrompt], $request->messages);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->withoutVerifying()->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.1-8b-instant',
                'messages'    => $messages,
                'max_tokens'  => 2048,
                'temperature' => 0.7,
            ]);

            if ($response->failed()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'error'  => $response->json('error.message', $response->body()),
                ]);
                return response()->json([
                    'reply' => 'Maaf, layanan AI sedang tidak tersedia. Coba lagi sebentar ya.'
                ], 502);
            }

            $data  = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, aku belum dapat jawaban untuk itu.';

            return response()->json(['reply' => trim($reply)]);

        } catch (\Exception $e) {
            Log::error('Groq chat exception', ['message' => $e->getMessage()]);
            return response()->json([
                'reply' => 'Terjadi kesalahan teknis. Silakan coba lagi.'
            ], 500);
        }
    }
}
