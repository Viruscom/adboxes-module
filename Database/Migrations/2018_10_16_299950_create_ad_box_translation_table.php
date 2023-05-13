<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdBoxTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_box_translation', static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ad_box_id')->unsigned();
            $table->string('locale')->index();
            $table->string('title');
            $table->string('label')->nullable()->default(null);
            $table->text('short_description')->nullable()->default(null);
            $table->text('url')->nullable()->default(null);
            $table->boolean('external_url')->default(false);
            $table->boolean('visible')->default(true);
            $table->timestamps();

            $table->unique(['ad_box_id', 'locale']);
            $table->foreign('ad_box_id')->references('id')->on('ad_boxes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_box_translation');
    }
}
