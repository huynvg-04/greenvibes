<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullProductVariantsSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên thuộc tính (VD: Size, Color)');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value')->comment('Giá trị hiển thị (VD: Size M)');
            $table->string('code')->nullable()->comment('Mã màu hex hoặc code viết tắt (VD: #FFFFFF)');
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->string('sku', 50)->unique()->comment('Mã SKU riêng biệt (VD: AO-THUN-XANH-M)');
            $table->string('name')->nullable()->comment('Tên đầy đủ của phân loại (tự động ghép hoặc nhập tay)');
            $table->string('image')->nullable()->comment('Ảnh riêng của phân loại này (nếu khác màu)');

            $table->integer('quantity')->default(0)->comment('Số lượng tồn kho thực tế');


            $table->decimal('standard_cost', 15, 2)->default(0)->comment('Giá vốn hàng bán');

            $table->decimal('list_price', 15, 2)->default(0)->comment('Giá niêm yết');

            $table->decimal('compare_at_price', 15, 2)->nullable()->comment('Giá thị trường/Giá cũ');

            $table->timestamps();
        });

        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade');

            $table->unique(['product_variant_id', 'attribute_value_id'], 'variant_val_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
}
