<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(string $subject, string $body): array
    {
        // Dummy mode (if AI disabled in .env)
        if (!config('services.openai.classify_enabled')) {
            $categories = ['billing', 'technical', 'general'];
            return [
                'category'    => $categories[array_rand($categories)],
                'explanation' => 'Dummy classification (AI disabled)',
                'confidence'  => round(mt_rand(70, 95) / 100, 2),
            ];
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a support ticket classifier. You must return ONLY valid JSON with these keys:
- "category": one of exactly ["billing", "technical", "general"]
- "explanation": a short text explaining the classification
- "confidence": a number between 0 and 1

Do not invent new categories. Always use exactly one of: billing, technical, general.',
                ],
                [
                    'role' => 'user',
                    'content' => "Subject: {$subject}\nBody: {$body}",
                ],
            ],
            'response_format' => ['type' => 'json_object'],
        ]);

        $content = $response->choices[0]->message->content ?? '{}';
        $parsed = json_decode($content, true);

        return $parsed ?: [
            'category'    => 'general',
            'explanation' => 'Could not parse AI response',
            'confidence'  => 0.5,
        ];
    }
}