<?php

use App\Models\Gateway;
use Illuminate\Database\Migrations\Migration;

class RemoveCyberSourceGateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // No longer supported for V2 Omnipay

        if (Gateway::count() > 0) {
            $cyber = Gateway::where('provider', 'Cybersource')->first();
            $cyber->payment_library_id = 2;
            $cyber->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void {}
}
