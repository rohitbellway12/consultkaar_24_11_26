<?php

namespace App\Services;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Get default and stored configuration.
     *
     * @return array
     */
    public static function getConfig()
    {
        $setting = BusinessSetting::where('key', 'ai_chat_config')->first();
        $stored = $setting && $setting->value ? json_decode($setting->value, true) : [];

        $defaults = [
            'status' => 1,
            'api_key' => env('OPENAI_API_KEY', ''),
            'api_endpoint' => env('AI_API_ENDPOINT', 'https://api.openai.com/v1'),
            'model' => 'gpt-6-luna',
            'duration_minutes' => 5,
            'cooldown_hours' => 24,
            'max_messages' => 30,
            'recommendation_count' => 3,
            'recommendation_filter' => 'rating', // 'rating', 'latest', 'random'
            'system_prompt' => "You are Consultkaar's AI Consultation Assistant powered by GPT-6 Luna. You provide empathetic, accurate, and structured advice based on the user's selected category. You do not replace human doctors or advisors, but you guide users constructively. Keep replies concise and easy to understand on mobile screens. Never give risky medical prescriptions.",
        ];

        return array_merge($defaults, is_array($stored) ? $stored : []);
    }

    /**
     * Save AI chat configuration to BusinessSetting.
     *
     * @param array $data
     * @return void
     */
    public static function saveConfig(array $data)
    {
        $current = self::getConfig();
        $merged = array_merge($current, $data);

        $setting = BusinessSetting::where('key', 'ai_chat_config')->first();
        if (!$setting) {
            $setting = new BusinessSetting();
            $setting->key = 'ai_chat_config';
        }
        $setting->value = json_encode($merged);
        $setting->save();

        if (isset($data['status'])) {
            $statusSetting = BusinessSetting::where('key', 'ai_chat_status')->first();
            if (!$statusSetting) {
                $statusSetting = new BusinessSetting();
                $statusSetting->key = 'ai_chat_status';
            }
            $statusSetting->value = json_encode(['status' => (int)$data['status']]);
            $statusSetting->save();
        }
    }

    /**
     * Check if AI chat service is enabled.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        $config = self::getConfig();
        return !empty($config['status']) && (int)$config['status'] === 1;
    }

    /**
     * Send conversation to OpenAI API and get reply.
     *
     * @param array $formattedMessages Array of ['role' => 'user'|'assistant'|'system', 'content' => string]
     * @param string|null $categoryName
     * @return array
     */
    public static function sendChat(array $formattedMessages, $categoryName = null)
    {
        $config = self::getConfig();
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return [
                'success' => false,
                'reply' => null,
                'tokens' => 0,
                'error' => 'OpenAI API key is not configured in Admin Settings.',
            ];
        }

        $systemPrompt = $config['system_prompt'] ?? '';
        if ($categoryName) {
            $systemPrompt .= "\n\nThe user is asking for assistance specifically within the category: \"{$categoryName}\". Tailor your answers accordingly.";
        }

        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];

        foreach ($formattedMessages as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => (string)($msg['content'] ?? ''),
            ];
        }

        $endpoint = rtrim($config['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';
        $model = $config['model'] ?? 'gpt-6-luna';

        $payload = [
            'model' => $model,
            'messages' => $messages,
        ];

        if (str_contains($model, 'luna') || str_contains($model, 'o1') || str_contains($model, 'gpt-5') || str_contains($model, 'gpt-6')) {
            $payload['max_completion_tokens'] = 800;
        } else {
            $payload['temperature'] = 0.7;
            $payload['max_tokens'] = 800;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(35)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? '';
                $tokens = $data['usage']['total_tokens'] ?? 0;

                return [
                    'success' => true,
                    'reply' => trim($reply),
                    'tokens' => $tokens,
                    'error' => null,
                ];
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI API Error: ' . $errorMsg);

            return [
                'success' => false,
                'reply' => null,
                'tokens' => 0,
                'error' => 'AI Service error: ' . $errorMsg,
            ];
        } catch (\Exception $e) {
            Log::error('AIService Exception: ' . $e->getMessage());

            return [
                'success' => false,
                'reply' => null,
                'tokens' => 0,
                'error' => 'Connection to AI failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate 2-sentence summary and advisor keywords from messages.
     *
     * @param array $messages
     * @param string|null $categoryName
     * @return array
     */
    public static function generateSummary(array $messages, $categoryName = null)
    {
        $config = self::getConfig();
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey) || empty($messages)) {
            return [
                'summary' => 'User had a 5-minute consultation regarding ' . ($categoryName ?? 'general inquiry') . '.',
                'keywords' => [],
            ];
        }

        $conversationText = '';
        foreach ($messages as $m) {
            $sender = ($m['sender'] ?? 'user') === 'user' ? 'User' : 'AI';
            $conversationText .= "{$sender}: {$m['message']}\n";
        }

        $prompt = "Given the following conversation between a user and an AI consultant in category '{$categoryName}', generate a 2-3 sentence executive clinical/advisory summary of the user's primary concern, and list 2-3 specialty keywords for human advisor recommendation.\n\n"
            . "Conversation:\n{$conversationText}\n\n"
            . "Respond in JSON format with exactly two keys: 'summary' (string) and 'keywords' (array of strings). Example: {\"summary\": \"...\", \"keywords\": [\"Dermatology\", \"Skin\"]}";

        $endpoint = rtrim($config['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';
        $model = $config['model'] ?? 'gpt-6-luna';

        $summaryPayload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a clinical summarizer for Consultkaar. Respond ONLY in valid JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        if (str_contains($model, 'luna') || str_contains($model, 'o1') || str_contains($model, 'gpt-5') || str_contains($model, 'gpt-6')) {
            $summaryPayload['max_completion_tokens'] = 300;
        } else {
            $summaryPayload['temperature'] = 0.3;
            $summaryPayload['max_tokens'] = 300;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(25)
                ->post($endpoint, $summaryPayload);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content') ?? '';
                // Clean json markdown if any
                $content = trim(preg_replace('/```json|```/', '', $content));
                $parsed = json_decode($content, true);

                if (is_array($parsed) && isset($parsed['summary'])) {
                    return [
                        'summary' => $parsed['summary'],
                        'keywords' => $parsed['keywords'] ?? [],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('AIService generateSummary error: ' . $e->getMessage());
        }

        return [
            'summary' => 'User completed an AI consultation regarding ' . ($categoryName ?? 'general inquiry') . '.',
            'keywords' => [],
        ];
    }
}
