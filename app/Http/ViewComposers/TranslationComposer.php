<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
     *
     */
    public function compose(View $view): void
    {
        $view->with('industries', Cache::get('industries')->each(function ($industry): void {
            $industry->name = trans('texts.industry_' . $industry->name);
        })->sortBy(fn ($industry) => $industry->name));

        $view->with('countries', Cache::get('countries')->each(function ($country): void {
            $country->name = trans('texts.country_' . $country->name);
        })->sortBy(fn ($country) => $country->name));

        $view->with('paymentTypes', Cache::get('paymentTypes')->each(function ($pType): void {
            $pType->name = trans('texts.payment_type_' . $pType->name);
        })->sortBy(fn ($pType) => $pType->name));

        $view->with('languages', Cache::get('languages')->each(function ($lang): void {
            $lang->name = trans('texts.lang_' . $lang->name);
        })->sortBy(fn ($lang) => $lang->name));

        $view->with('currencies', Cache::get('currencies')->each(function ($currency): void {
            $currency->name = trans('texts.currency_' . Str::slug($currency->name, '_'));
        })->sortBy(fn ($currency) => $currency->name));
    }
}
