<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around Google's Gemini generative-language REST API.
 * Every AI feature in the app goes through here so there is one place
 * that owns the API key, model name, timeout, and error handling.
 */
class GeminiService
{
    private ?string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Single-turn generation: one system instruction + one user prompt in,
     * plain text out. Used by the daily briefing and restock recommendation
     * features, which don't need conversation history.
     */
    public function generate(string $prompt, ?string $systemInstruction = null, float $temperature = 0.4): ?string
    {
        return $this->generateFromContents(
            [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            $systemInstruction,
            $temperature
        );
    }

    /**
     * Multi-turn generation for the chat assistant. $history is an array of
     * ['role' => 'user'|'model', 'text' => string] in chronological order.
     */
    public function chat(array $history, string $newMessage, ?string $systemInstruction = null): ?string
    {
        $contents = array_map(fn ($m) => [
            'role' => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['text']]],
        ], $history);

        $contents[] = ['role' => 'user', 'parts' => [['text' => $newMessage]]];

        return $this->generateFromContents($contents, $systemInstruction, 0.6);
    }

    private function generateFromContents(array $contents, ?string $systemInstruction, float $temperature): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('GeminiService: GEMINI_API_KEY is not set, skipping AI call.');
            return null;
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => 2048,
                // These are short, non-reasoning tasks (summaries, chat
                // replies) — disable "thinking" mode so the token budget
                // goes entirely to the actual answer instead of internal
                // reasoning tokens, and responses come back faster.
                'thinkingConfig' => ['thinkingBudget' => 0],
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout(25)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", $payload);

                if ($response->successful()) {
                    $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
                    return $text ? trim($text) : null;
                }

                // 503 = model temporarily overloaded on Google's side, worth a
                // short retry. Anything else (401/400/etc.) won't succeed on
                // retry, so fail fast instead of wasting quota.
                if ($response->status() !== 503 || $attempt === $maxAttempts) {
                    Log::warning('GeminiService: API request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return null;
                }

                usleep(1_200_000);
            } catch (\Throwable $e) {
                Log::error('GeminiService: exception during API call: ' . $e->getMessage());
                return null;
            }
        }

        return null;
    }
}
