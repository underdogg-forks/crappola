<?php
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        Eloquent::unguard();
        // https://github.com/caouecs/Laravel-lang
        // https://www.loc.gov/standards/iso639-2/php/code_list.php
        $languages = [
            ['name' => 'English', 'locale' => 'en'],
        ];
        foreach ($languages as $language) {
            $record = Language::whereLocale($language['locale'])->first();
            if ($record) {
                $record->name = $language['name'];
                $record->save();
            } else {
                Language::create($language);
            }
        }
        Eloquent::reguard();
    }
}
