<?php

namespace App\Providers;

use App\Http\ViewComposers\AppLanguageComposer;
use App\Http\ViewComposers\ClientPortalHeaderComposer;
use App\Http\ViewComposers\ProposalComposer;
use App\Http\ViewComposers\TranslationComposer;
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
                'accounts.details',
                'clients.edit',
                'vendors.edit',
                'payments.edit',
                'invoices.edit',
                'expenses.edit',
                'accounts.localization',
                'payments.credit_card',
                'invited.details',
            ],
            TranslationComposer::class
        );

        view()->composer(
            [
                'header',
                'tasks.edit',
            ],
            AppLanguageComposer::class
        );

        view()->composer(
            [
                'public.header',
            ],
            ClientPortalHeaderComposer::class
        );

        view()->composer(
            [
                'proposals.edit',
                'proposals.templates.edit',
                'proposals.snippets.edit',
            ],
            ProposalComposer::class
        );
    }

    /**
     * Register the service provider.
     */
    public function register(): void {}
}
