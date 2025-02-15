<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFontSupport extends Migration
{
    public function up()
    {
        Schema::create('fonts', function ($t) {
            $t->increments('id');

            $t->string('name');
            $t->string('folder');
            $t->string('css_stack');
            $t->smallInteger('css_weight')->default(400);
            $t->string('google_font');
            $t->string('normal');
            $t->string('bold');
            $t->string('italics');
            $t->string('bolditalics');
            $t->boolean('is_early_access');
            $t->unsignedInteger('sort_order')->default(10000);
        });

        // Create fonts
        //$seeder = new FontsSeeder();
        //$seeder->run();

        Schema::table('accounts', function ($table) {});

        /*
        Schema::table('accounts', function ($table) {
              $table->foreign('header_font_id')->references('id')->on('fonts');
            $table->foreign('body_font_id')->references('id')->on('fonts');
        });
        */
    }

    public function down()
    {
        if (Schema::hasColumn('accounts', 'header_font_id')) {
            Schema::table('accounts', function ($table) {
                //$table->dropForeign('accounts_header_font_id_foreign');
                $table->dropColumn('header_font_id');
            });
        }

        if (Schema::hasColumn('accounts', 'body_font_id')) {
            Schema::table('accounts', function ($table) {
                //$table->dropForeign('accounts_body_font_id_foreign');
                $table->dropColumn('body_font_id');
            });
        }

        Schema::dropIfExists('fonts');
    }
}
