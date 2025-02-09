<?php

use Illuminate\Database\Migrations\Migration;

class AddClientViewCss extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->text('client_view_css')->nullable();
        });
    }

    public function down()
    {
        Schema::table('accounts', function ($table) {
            $table->dropColumn('client_view_css');
        });
    }
}
