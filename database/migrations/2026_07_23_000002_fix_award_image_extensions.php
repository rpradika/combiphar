<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Repair 10 award rows broken by 35cf235: a local images:optimize run had
 * converted these PNGs to JPG, but the .jpg twins were gitignored — so that
 * commit shipped the .png DELETIONS without their replacements, leaving dev
 * pointing at files that no longer exist (403s in the award popup). The .jpg
 * files ride in git with this commit; this repoints each env's rows.
 * Idempotent: exact-match update, no-op once a row already says .jpg.
 */
return new class extends Migration
{
    private const RENAMED = [
        'awards/2018/superbrand-indonesia-award-2018-insto-514800',
        'awards/2021/watson-hwb-awards-2021-best-selling-eye-drop-eye-mo-singapore-8f19c1',
        'awards/2022/indonesia-healthcare-award-2022-obh-combi-anak-9cec07',
        'awards/2022/jdid-award-2022-air-mancur-8e2e0a',
        'awards/2022/top-brand-award-madurasa-2022-70b134',
        'awards/2022/watsons-hwb-award-best-selling-moisturizing-eye-drop-2022-eye-mo-singapore-2fd0b2',
        'awards/2025/marketeers-editor-s-choice-award-meca-experiential-marketing-campaign-of-the-year-simba-9c9e26',
        'awards/2026/madurasa-omni-brand-2026-online-offline-new-product-campaign-bfddf7',
        'awards/2026/madurasa-omni-brand-2026-seasonal-integrated-marketing-campaign-b0d1d5',
        'awards/2026/supermom-brand-awards-2026-simba-6425d8',
    ];

    public function up(): void
    {
        foreach (self::RENAMED as $base) {
            DB::table('awards')->where('image', "{$base}.png")->update(['image' => "{$base}.jpg"]);
        }
    }

    public function down(): void
    {
        // Files only exist as .jpg now — reverting the paths would re-break them.
    }
};
