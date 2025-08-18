<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('epaper_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epaper_id')->constrained()->onDelete('cascade');
            $table->integer('page_number');
            $table->string('image_path');
            $table->string('thumbnail_path');
            $table->timestamps();
            
            $table->unique(['epaper_id', 'page_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('epaper_pages');
    }
};