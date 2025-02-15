<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddMigrationFlagForCompaniesTable extends Migration
{
    public function up()
    {
        Schema::table('companies', function ($table) {
            $table->boolean('is_migrated')->default(false);
        });
    }

    public function down() {}
}
