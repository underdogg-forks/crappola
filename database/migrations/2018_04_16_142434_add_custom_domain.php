<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCustomDomain extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->boolean('is_custom_domain')->default(false);
        });
    }

    public function down() {}
}
