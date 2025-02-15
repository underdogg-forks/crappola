<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddOptionForProductNotes extends Migration
{
    public function up()
    {
        Schema::table('accounts', function ($table) {
            $table->boolean('show_product_notes')->default(false);
        });
    }

    public function down() {}
}
