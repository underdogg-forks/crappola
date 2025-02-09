<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddCascaseDrops extends Migration
{
    public function up()
    {
        Schema::table('invoices', function ($table) {
            $table->dropForeign('invoices_account_id_foreign');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down() {}
}
