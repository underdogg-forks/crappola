<?php

use Illuminate\Database\Migrations\Migration;

class AddDanishTranslation extends Migration
{
    public function up()
    {
        //DB::table('languages')->insert(['name' => 'Danish', 'locale' => 'da']);
    }

    public function down()
    {
        //$language = \App\Models\Language::whereLocale('da')->first();
        //$language->delete();
    }
}
