<?php

namespace App\Http\Controllers;

use Codedge\Updater\UpdaterManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Utils;

class SelfUpdateController extends BaseController
{
    protected UpdaterManager $updater;

    /**
     * SelfUpdateController constructor.
     *
     * @param UpdaterManager $updater
     */
    public function __construct(UpdaterManager $updater)
    {
        if (Utils::isNinjaProd()) {
            exit;
        }

        $this->updater = $updater;
    }

    /**
     * Show default update page.
     *
     * @return mixed
     */
    public function index()
    {
        $versionInstalled = $this->updater->source()->getVersionInstalled('v');
        $updateAvailable = $this->updater->source()->isNewVersionAvailable($versionInstalled);

        return view(
            'vendor.self-update.self-update',
            [
                'versionInstalled' => $versionInstalled,
                'versionAvailable' => $this->updater->source()->getVersionAvailable(),
                'updateAvailable'  => $updateAvailable,
            ]
        );
    }

    /**
     * Run the actual update.
     *
     * @return RedirectResponse
     */
    public function update()
    {
        $this->updater->source()->update();

        return Redirect::to('/');
    }

    public function download(): void
    {
        $this->updater->source()->fetch();
    }
}
