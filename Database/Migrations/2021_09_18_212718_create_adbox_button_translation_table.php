<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdboxButtonTranslationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('ad_box_button_translation', static function (Blueprint $table) {
            $table->id();
            $table->integer('ad_box_type');
            $table->string('locale')->index();
            $table->string('title');
            $table->text('url')->nullable()->default(null);
            $table->boolean('external_url')->default(false);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_box_button_translation');
    }
}
