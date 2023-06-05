<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\AdBoxes\Models\AdBox;

class CreateAdBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_boxes', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->default(AdBox::$FIRST_TYPE);
            $table->integer('position');
            $table->boolean('active')->default(true);
            $table->boolean('from_price')->default(false);
            $table->boolean('from_new_price')->default(false);
            $table->decimal('price', 10,2)->default(0);
            $table->decimal('new_price', 10,2)->default(0);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('date')->nullable();
            $table->string('type_color_class')->nullable();
            $table->string('filename')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_boxes');
    }
}
