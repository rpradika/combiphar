<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `pages.under_development` toggle (CMS). When on for the Investor page, the
     * Investor page shows its "Segera Hadir" overlay and the Berita "Investor
     * Update" tab shows the same coming-soon block; when off, both show real
     * content. Seed the Investor page to true so current behavior is preserved
     * on deploy (both currently always show coming-soon).
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('under_development')->default(false)->after('slug');
        });

        DB::table('pages')->where('slug', 'investor')->update(['under_development' => true]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('under_development');
        });
    }
};
