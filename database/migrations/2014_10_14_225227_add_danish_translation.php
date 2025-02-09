<?php

use Illuminate\Database\Migrations\Migration;

class AddDanishTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //DB::table('languages')->insert(['name' => 'Danish', 'locale' => 'da']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //$language = \App\Models\Language::whereLocale('da')->first();
        //$language->delete();
    }
}
