<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use Codedge\Updater\UpdaterManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class SelfUpdateController extends BaseController
{
    /**
     * @var UpdaterManager
     */
    protected $updater;

    /**
     * SelfUpdateController constructor.
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
