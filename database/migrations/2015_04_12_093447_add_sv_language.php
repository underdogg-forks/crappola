<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //DB::table('languages')->insert(['name' => 'Swedish', 'locale' => 'sv']);
        //DB::table('languages')->insert(['name' => 'Spanish - Spain', 'locale' => 'es_ES']);
        //DB::table('languages')->insert(['name' => 'French - Canada', 'locale' => 'fr_CA']);
        //DB::table('languages')->insert(['name' => 'Lithuanian', 'locale' => 'lt']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if ($language = \App\Models\Language::whereLocale('sv')->first()) {
            $language->delete();
        }

        if ($language = \App\Models\Language::whereLocale('es_ES')->first()) {
            $language->delete();
        }

        if ($language = \App\Models\Language::whereLocale('fr_CA')->first()) {
            $language->delete();
        }

        if ($language = \App\Models\Language::whereLocale('lt')->first()) {
            $language->delete();
        }
    }
};
