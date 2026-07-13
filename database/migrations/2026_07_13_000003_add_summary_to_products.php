<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // short blurb shown on the product card (desktop); full description stays for the detail popup
            $table->text('summary_id')->nullable()->after('name_en');
            $table->text('summary_en')->nullable()->after('summary_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['summary_id', 'summary_en']);
        });
    }
};
