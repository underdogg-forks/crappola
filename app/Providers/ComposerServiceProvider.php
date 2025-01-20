<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot(): void
    {
        view()->composer(
            [
                'companies.details',
                'clients.edit',
                'vendors.edit',
                'payments.edit',
                'invoices.edit',
                'expenses.edit',
                'companies.localization',
                'payments.credit_card',
                'invited.details',
            ],
            'App\Http\ViewComposers\TranslationComposer'
        );

        view()->composer(
            [
                'header',
                'tasks.edit',
            ],
            'App\Http\ViewComposers\AppLanguageComposer'
        );

        view()->composer(
            [
                'public.header',
            ],
            'App\Http\ViewComposers\ClientPortalHeaderComposer'
        );

        view()->composer(
            [
                'proposals.edit',
                'proposals.templates.edit',
                'proposals.snippets.edit',
            ],
            'App\Http\ViewComposers\ProposalComposer'
        );
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
    }
}
