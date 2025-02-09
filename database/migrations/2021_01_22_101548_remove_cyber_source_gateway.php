<?php

use App\Models\Gateway;
use Illuminate\Database\Migrations\Migration;

class RemoveCyberSourceGateway extends Migration
{
    public function up()
    {
        // No longer supported for V2 Omnipay

        if(Gateway::count() > 0) {
            $cyber = Gateway::where('provider', 'Cybersource')->first();
            $cyber->payment_library_id = 2;
            $cyber->save();
        }
    }

    public function down() {}
}
