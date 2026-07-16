<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('csr_programs', function (Blueprint $table) {
            // Detail-page layout variant: null/'default' = article + contact form,
            // 'gallery' = photo grid (mis. Environmental), 'slider' = program slider (mis. Social Care).
            $table->string('layout')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('csr_programs', function (Blueprint $table) {
            $table->dropColumn('layout');
        });
    }
};
