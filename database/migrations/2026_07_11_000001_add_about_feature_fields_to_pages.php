<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $t) {
            foreach (['manufacturing_title', 'manufacturing_body', 'international_title', 'international_body'] as $f) {
                $t->text($f . '_id')->nullable();
                $t->text($f . '_en')->nullable();
            }
            $t->string('manufacturing_image')->nullable();
            $t->string('international_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $t) {
            $t->dropColumn([
                'manufacturing_title_id', 'manufacturing_title_en',
                'manufacturing_body_id', 'manufacturing_body_en',
                'international_title_id', 'international_title_en',
                'international_body_id', 'international_body_en',
                'manufacturing_image', 'international_image',
            ]);
        });
    }
};
