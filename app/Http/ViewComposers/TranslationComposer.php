<?php

namespace App\Http\ViewComposers;

use Cache;
use Illuminate\View\View;
use Str;

/**
 * TranslationComposer.php.
 *
 * @copyright See LICENSE file that was distributed with this source code.
 */
class TranslationComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view): void
    {
        $view->with('industries', \Illuminate\Support\Facades\Cache::get('industries')->each(function ($industry): void {
            $industry->name = trans('texts.industry_' . $industry->name);
        })->sortBy(function ($industry) {
            return $industry->name;
        }));

        $view->with('countries', \Illuminate\Support\Facades\Cache::get('countries')->each(function ($country): void {
            $country->name = trans('texts.country_' . $country->name);
        })->sortBy(function ($country) {
            return $country->name;
        }));

        $view->with('paymentTypes', \Illuminate\Support\Facades\Cache::get('paymentTypes')->each(function ($pType): void {
            $pType->name = trans('texts.payment_type_' . $pType->name);
        })->sortBy(function ($pType) {
            return $pType->name;
        }));

        $view->with('languages', \Illuminate\Support\Facades\Cache::get('languages')->each(function ($lang): void {
            $lang->name = trans('texts.lang_' . $lang->name);
        })->sortBy(function ($lang) {
            return $lang->name;
        }));

        $view->with('currencies', \Illuminate\Support\Facades\Cache::get('currencies')->each(function ($currency): void {
            $currency->name = trans('texts.currency_' . \Illuminate\Support\Str::slug($currency->name, '_'));
        })->sortBy(function ($currency) {
            return $currency->name;
        }));
    }
}
