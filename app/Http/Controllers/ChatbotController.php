<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ChatMessage;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        
        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');
        $userId = Auth::id();

        try {
            $historyForAI = [];

            if ($userId) {
                $dbMessages = ChatMessage::where('user_id', $userId)
                                         ->orderBy('created_at', 'desc')
                                         ->take(10)
                                         ->get()
                                         ->reverse(); 

                foreach ($dbMessages as $msg) {
                    $historyForAI[] = [
                        'role' => $msg->role,
                        'parts' => [['text' => $msg->content]]
                    ];
                }
            } else {
                $clientHistory = $request->input('history', []);
                foreach ($clientHistory as $msg) {
                    if (isset($msg['role'], $msg['content'])) {
                        $historyForAI[] = [
                            'role' => ($msg['role'] == 'bot') ? 'model' : 'user',
                            'parts' => [['text' => $msg['content']]]
                        ];
                    }
                }
            }

            $queryVector = $this->getEmbedding($userMessage);
            $products = collect([]);

            if ($queryVector) {
                $allProducts = Product::whereNotNull('embedding')
                    ->where('status', 1)
                    ->get(['id', 'embedding']);

                $ranked = $allProducts->map(function ($item) use ($queryVector) {
                    $itemVector = json_decode($item->embedding);
                    $item->similarity = $this->cosineSimilarity($queryVector, $itemVector);
                    return $item;
                })->sortByDesc('similarity');

                $topIds = $ranked->filter(fn($i) => $i->similarity > 0.45)->take(4)->pluck('id');

                if ($topIds->isNotEmpty()) {
                    $products = Product::with([
                        'variants' => function ($q) { $q->where('stock', '>', 0); },
                        'variants.attributeValues.attribute',
                        'tags'
                    ])->whereIn('id', $topIds)->get();
                }
            }

            if ($products->isEmpty()) {
                $products = Product::where('name', 'like', "%{$userMessage}%")
                    ->with(['variants.attributeValues.attribute'])
                    ->take(3)->get();
            }

            $productContext = "";
            if ($products->isNotEmpty()) {
                $productContext = "DƯỚI ĐÂY LÀ DANH SÁCH SẢN PHẨM TÌM THẤY:\n";
                foreach ($products as $p) {
                    $soldTotal = ($p->sold_count > 0) ? " (Đã bán: {$p->sold_count})" : "";
                    
                    $link = route('products.show', $p->slug); 

                    $productContext .= "- Tên: {$p->name}{$soldTotal}\n";
                    $productContext .= "  Link chi tiết: {$link}\n"; 

                    if ($p->variants->isNotEmpty()) {
                        foreach ($p->variants as $v) {
                            $attrs = $v->attributeValues->map(fn($val) => $val->value)->implode(' ');
                            $price = number_format($v->sale_price ?? $v->list_price, 0, ',', '.');
                            $vSold = ($v->sold_count > 0) ? " - Đã bán: {$v->sold_count}" : "";
                            $productContext .= "  + [{$attrs}]: {$price}đ{$vSold}\n";
                        }
                    } else {
                        $price = number_format($p->sale_price ?? $p->list_price, 0, ',', '.');
                        $productContext .= "  + Giá: {$price}đ\n";
                    }
                    $productContext .= "  Mô tả: " . \Illuminate\Support\Str::limit(strip_tags($p->description), 100) . "\n\n";
                }
                
                $productContext .= "YÊU CẦU: \n";
                $productContext .= "1. Chỉ tư vấn dựa trên danh sách trên.\n";
                $productContext .= "2. Khi khách hỏi chi tiết hoặc muốn mua, HÃY GỬI KÈM đường dẫn (Link chi tiết) của sản phẩm đó.\n";
                $productContext .= "3. Nếu sản phẩm có số lượng bán cao, hãy giới thiệu là sản phẩm HOT.\n";
            } else {
                $productContext = "Không tìm thấy sản phẩm khớp. Hãy tư vấn chung hoặc gợi ý xem menu.\n";
            }

            $systemPrompt = "Bạn là trợ lý cho cửa hàng cây cảnh GreenVibes. Giọng thân thiện 🌿.\n" .
                $productContext .
                "Khách hàng hỏi: " . $userMessage . "\n" .
                "Trả lời ngắn gọn (<150 từ):";

            $contents = $historyForAI;
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemPrompt]]
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key={$apiKey}", [
                    'contents' => $contents,
                    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 2000]
                ]);

            $responseBody = $response->json();

            if (isset($responseBody['error'])) {
                Log::error("GEMINI ERROR: " . json_encode($responseBody['error'], JSON_UNESCAPED_UNICODE));
                return response()->json(['reply' => 'Hệ thống đang gặp sự cố kết nối AI (Xem log để biết chi tiết).']);
            }

            Log::info("GEMINI RESPONSE RAW: " . json_encode($responseBody, JSON_UNESCAPED_UNICODE));

            // trích xuất câu trả lời
            $reply = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$reply) {
                $finishReason = $responseBody['candidates'][0]['finishReason'] ?? 'UNKNOWN';
                Log::warning("Gemini không trả lời. Lý do (Finish Reason): " . $finishReason);

                $reply = 'Xin lỗi, tôi chưa hiểu ý bạn hoặc nội dung câu hỏi không phù hợp với tiêu chuẩn an toàn.';
            }

            if ($userId) {
                ChatMessage::create([
                    'user_id' => $userId, 
                    'role' => 'user', 
                    'content' => $userMessage
                ]);
                ChatMessage::create([
                    'user_id' => $userId, 
                    'role' => 'model', 
                    'content' => $reply
                ]);
            }

            return response()->json([
                'reply' => $reply,
                'is_logged_in' => !!$userId 
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['reply' => 'Hệ thống đang bảo trì.'], 500);
        }
    }

    public function history()
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $messages = ChatMessage::where('user_id', Auth::id())
                               ->orderBy('created_at', 'asc')
                               ->take(20)
                               ->get(['role', 'content']);
        
        return response()->json($messages);
    }

    private function getEmbedding($text)
    {
        $apiKey = env('GEMINI_API_KEY');
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$apiKey}", [
                'model' => 'models/text-embedding-004',
                'content' => ['parts' => [['text' => $text]]]
            ]);
        return $response->json()['embedding']['values'] ?? null;
    }

    private function cosineSimilarity($vecA, $vecB)
    {
        $dot = 0; $magA = 0; $magB = 0;
        foreach ($vecA as $i => $val) {
            if (!isset($vecB[$i])) continue;
            $dot += $val * $vecB[$i];
            $magA += $val * $val;
            $magB += $vecB[$i] * $vecB[$i];
        }
        return ($magA * $magB) == 0 ? 0 : $dot / (sqrt($magA) * sqrt($magB));
    }
}