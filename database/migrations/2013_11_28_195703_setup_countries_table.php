<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SetupCountriesTable extends Migration
{
    public function up()
    {
        // Creates the users table
        /*
        Schema::create('countries', function($table)
        {
            $table->integer('id')->index();
            $table->string('capital', 255)->nullable();
            $table->string('citizenship', 255)->nullable();
            $table->string('country_code', 3)->default('');
            $table->string('currency', 255)->nullable();
            $table->string('currency_code', 255)->nullable();
            $table->string('currency_sub_unit', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('iso_3166_2', 2)->default('');
            $table->string('iso_3166_3', 3)->default('');
            $table->string('name', 255)->default('');
            $table->string('region_code', 3)->default('');
            $table->string('sub_region_code', 3)->default('');
            $table->boolean('eea')->default(0);

            $table->primary('id');
        });
        */
    }

    public function down()
    {
        //Schema::drop('countries');
    }
}
