<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\InvoiceStatus;

class InvoiceStatusSeeder extends Seeder
{
    public function run(): void
    {
        

        $this->createInvoiceStatuses();

        Eloquent::reguard();
    }

    private function createInvoiceStatuses(): void
    {
        $statuses = [
            ['id' => '1', 'name' => 'Draft'],
            ['id' => '2', 'name' => 'Sent'],
            ['id' => '3', 'name' => 'Viewed'],
            ['id' => '4', 'name' => 'Approved'],
            ['id' => '5', 'name' => 'Partial'],
            ['id' => '6', 'name' => 'Paid'],
        ];

        foreach ($statuses as $status) {
            $record = InvoiceStatus::find($status['id']);
            if ($record) {
                $record->name = $status['name'];
                $record->save();
            } else {
                InvoiceStatus::create($status);
            }
        }
    }
}
