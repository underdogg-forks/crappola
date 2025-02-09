<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddForwardUrlForV5 extends Migration
{
    public function up()
    {
        Schema::table('account_email_settings', function ($table) {
            $table->text('forward_url_for_v5')->default('');
            $table->boolean('is_disabled')->default(false);
        });
    }

    public function down() {}
}
