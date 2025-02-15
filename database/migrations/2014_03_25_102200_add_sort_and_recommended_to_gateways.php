<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddSortAndRecommendedToGateways extends Migration
{
    public function up()
    {
        Schema::table('gateways', function ($table) {});
    }

    public function down()
    {
        if (Schema::hasColumn('gateways', 'sort_order')) {
            Schema::table('gateways', function ($table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('gateways', 'recommended')) {
            Schema::table('gateways', function ($table) {
                $table->dropColumn('recommended');
            });
        }

        if (Schema::hasColumn('gateways', 'site_url')) {
            Schema::table('gateways', function ($table) {
                $table->dropColumn('site_url');
            });
        }
    }
}
