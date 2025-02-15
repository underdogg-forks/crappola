<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrackLastSeenMessage extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {});
        if (DB::table('payment_libraries')->count() > 0) {
            DB::table('gateways')->update(['recommended' => 0]);
            DB::table('gateways')->insert([
                'name'               => 'moolah',
                'provider'           => 'AuthorizeNet_AIM',
                'sort_order'         => 1,
                'recommended'        => 1,
                'site_url'           => 'https://invoiceninja.mymoolah.com/',
                'payment_library_id' => 1,
            ]);
        }
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('news_feed_id');
        });
    }
}
