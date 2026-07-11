<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_histories', function (Blueprint $t) {
            $t->id();
            $t->string('year')->nullable();
            $t->text('caption_id')->nullable();
            $t->text('caption_en')->nullable();
            $t->string('photo')->nullable();
            $t->integer('sort')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_histories');
    }
};
