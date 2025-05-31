<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompositionToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('composition')->nullable()->after('description'); // JSON для хранения состава
            $table->text('full_description')->nullable()->after('composition'); // Полное описание
            $table->string('code')->nullable()->after('quantity'); // Код продукта
            $table->boolean('is_active')->default(true)->after('code'); // Активность продукта
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['composition', 'full_description', 'code', 'is_active']);
        });
    }
}