<?php

namespace Database\Seeders;

use App\Models\DbServer;
use Illuminate\Database\Seeder;

class DbServerSeeder extends Seeder
{
    public function run(): void
    {
        Eloquent::unguard();

        $servers = [
            ['name' => 'db-ninja-1'],
            ['name' => 'db-ninja-2'],
        ];

        foreach ($servers as $server) {
            $record = DbServer::where('name', '=', $server['name'])->first();

            if ($record) {
                // do nothing
            } else {
                DbServer::create($server);
            }
        }
    }
}
