<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('languages', function ($table): void {
            $table->increments('id');
            $table->string('name');
            $table->string('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
