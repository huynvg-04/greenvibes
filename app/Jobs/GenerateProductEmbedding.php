<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateProductEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product->load([
            'category',
            'tags',
            'variants.attributeValues.attribute'
        ]);

        try {
            $fullText = "Tên sản phẩm: " . $product->name . ". ";

            $soldCount = $product->sold_count ?? 0;
            $fullText .= "Đã bán: {$soldCount} sản phẩm. ";

            if ($soldCount > 10) {
                $fullText .= "Đây là sản phẩm bán chạy, được nhiều khách hàng yêu thích. ";
            }

            if ($product->category) {
                $fullText .= "Danh mục: " . $product->category->name . ". ";
            }

            if ($product->tags->isNotEmpty()) {
                $fullText .= "Từ khóa/Đặc điểm: " . $product->tags->pluck('name')->implode(', ') . ". ";
            }

            $desc = strip_tags($product->description);
            $desc = preg_replace('/\s+/', ' ', $desc);
            $fullText .= "Mô tả: " . mb_substr($desc, 0, 2000) . ". ";

            if ($product->variants->isNotEmpty()) {
                $variantsInfo = [];
                foreach ($product->variants as $variant) {
                    $attrs = $variant->attributeValues->map(function ($val) {
                        return ($val->attribute->name ?? '') . ' ' . $val->value;
                    })->implode(' ');

                    $price = number_format($variant->sale_price ?? $variant->list_price, 0, ',', '.');
                    $stock = $variant->stock > 0 ? "còn hàng" : "hết hàng";

                    $varSold = $variant->sold_count ?? 0;

                    $variantsInfo[] = "[Phiên bản {$attrs} giá {$price}đ - Đã bán {$varSold} ({$stock})]";
                }
                $fullText .= "Các tùy chọn mua hàng: " . implode(', ', $variantsInfo) . ".";
            }

            Log::info("FullText của SP {$product->id}: " . $fullText);
            
            $vector = $this->getEmbedding($fullText);

            if ($vector) {
                $product->embedding = json_encode($vector);
                $product->saveQuietly();
                Log::info("Đã cập nhật Vector cho SP ID: {$product->id}");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi tạo Vector SP ID {$product->id}: " . $e->getMessage());
        }
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
}
