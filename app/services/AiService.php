<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiService
{
    public function chat(string $message, array $context = []): array
    {
        $system = <<<SYS
Bạn là trợ lý mua sắm tiếng Việt cho cửa hàng. Trả về JSON hợp lệ duy nhất, dạng:
{
  "text": "câu trả lời ngắn gọn cho người dùng",
  "actions": [
    { "type": "recommend", "payload": { "q": "áo thun", "category_id": 1, "min_price": 0, "max_price": 500000, "limit": 6 } },
    { "type": "add_to_cart", "payload": { "product_id": 123, "quantity": 2 } },
    { "type": "update_cart", "payload": { "product_id": 123, "quantity": 1 } },
    { "type": "create_checkout", "payload": { "address": "Hà Nội", "method": "vnpay" } }
  ]
}
Chỉ dùng 4 action: recommend, add_to_cart, update_cart, create_checkout.
Nếu không chắc, dùng recommend hoặc hỏi lại bằng "text".
SYS;

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => json_encode([
                'message' => $message,
                'context' => $context,
            ], JSON_UNESCAPED_UNICODE)]
        ];

        $base  = rtrim(env('OPENROUTER_API_BASE', 'https://openrouter.ai/api/v1'), '/');
        $model = env('OPENROUTER_MODEL', 'openai/gpt-4o-mini');

        $headers = [
            'Authorization' => 'Bearer '.env('OPENROUTER_API_KEY'),
            'Content-Type'  => 'application/json',
            'HTTP-Referer'  => env('OPENROUTER_SITE_URL', ''),
            'X-Title'       => env('OPENROUTER_APP_TITLE', 'Laravel Shop AI'),
        ];

        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => 0.2,
            'max_tokens'  => 700,
        ];

        $res = Http::withHeaders($headers)
            ->timeout(25)
            ->post("{$base}/chat/completions", $payload)
            ->throw();

        $content = data_get($res->json(), 'choices.0.message.content', '{}');

        try {
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $json = ['text' => (string)$content, 'actions' => []];
        }

        $json['text']    = $json['text']    ?? '';
        $json['actions'] = is_array($json['actions'] ?? null) ? $json['actions'] : [];

        $json['actions'] = array_values(array_filter($json['actions'], function ($a) {
            return in_array(($a['type'] ?? ''), ['recommend','add_to_cart','update_cart','create_checkout']);
        }));

        return $json;
    }
}
