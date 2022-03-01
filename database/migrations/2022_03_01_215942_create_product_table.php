<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Название');
            $table->string('vendor_code')->comment('Артикул');
            $table->float('price', 10, 2)->comment('Цена');
            $table->text('description')->nullable()->comment('Описание');
            $table->timestamps();

            # будет производиться поиск по названию
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
