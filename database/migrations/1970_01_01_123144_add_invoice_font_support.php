<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFontSupport extends Migration
{
    public function up(): void
    {
        Schema::create('fonts', function ($table): void {
            $table->increments('id');

            $table->string('name');
            $table->string('folder');
            $table->string('css_stack');
            $table->smallInteger('css_weight')->default(400);
            $table->string('google_font');
            $table->string('normal');
            $table->string('bold');
            $table->string('italics');
            $table->string('bolditalics');
            $table->boolean('is_early_access');
            $table->unsignedInteger('sort_order')->default(10000);
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('companies', 'header_font_id')) {
            Schema::table('companies', function ($table): void {
                //$table->dropForeign('accounts_header_font_id_foreign');
                $table->dropColumn('header_font_id');
            });
        }

        if (Schema::hasColumn('companies', 'body_font_id')) {
            Schema::table('companies', function ($table): void {
                //$table->dropForeign('accounts_body_font_id_foreign');
                $table->dropColumn('body_font_id');
            });
        }

        Schema::dropIfExists('fonts');
    }
}
