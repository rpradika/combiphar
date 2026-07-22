<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Splits the About "Pencapaian & Penghargaan" section in two:
     *  - Hero awards (`is_hero = true`) render as logos below the description.
     *  - The rest render inside the "Click here for details" popup, grouped by
     *    year (Figma 946:44).
     * Until any award is flagged hero the page falls back to showing all awards
     * as logos, so nothing disappears after deploy.
     */
    public function up(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->boolean('is_hero')->default(false)->after('year');
        });
    }

    public function down(): void
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->dropColumn('is_hero');
        });
    }
};
