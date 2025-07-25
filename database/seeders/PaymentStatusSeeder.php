<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PaymentStatusSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->createPaymentStatuses();

        Model::reguard();
    }

    private function createPaymentStatuses()
    {
        $statuses = [
            ['id' => '1', 'name' => 'Pending'],
            ['id' => '2', 'name' => 'Voided'],
            ['id' => '3', 'name' => 'Failed'],
            ['id' => '4', 'name' => 'Completed'],
            ['id' => '5', 'name' => 'Partially Refunded'],
            ['id' => '6', 'name' => 'Refunded'],
        ];

        foreach ($statuses as $status) {
            $record = PaymentStatus::find($status['id']);
            if ($record) {
                $record->name = $status['name'];
                $record->save();
            } else {
                PaymentStatus::create($status);
            }
        }
    }
}
