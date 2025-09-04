<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('epapers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('publication_date');
            $table->string('edition')->default('Odisha');
            $table->string('pdf_path')->nullable();
            $table->integer('total_pages')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['publication_date', 'edition']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('epapers');
    }
};