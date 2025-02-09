<?php

namespace App\Http\Controllers;

use App\Libraries\Utils;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;

    public $layout;

    protected $entityType;

    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout(): void
    {
        if (null !== $this->layout) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function returnBulk($entityType, $action, $ids): Application|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        if ( ! is_array($ids)) {
            $ids = [$ids];
        }

        $isDatatable = filter_var(request()->datatable, FILTER_VALIDATE_BOOLEAN);
        $referer = Request::server('HTTP_REFERER');
        $entityTypes = Utils::pluralizeEntityType($entityType);

        // when restoring redirect to entity
        if ($action == 'restore' && count($ids) == 1) {
            return redirect($entityTypes . '/' . $ids[0]);
            // when viewing from a datatable list
        }

        if (mb_strpos($referer, '/clients/') || mb_strpos($referer, '/projects/')) {
            return redirect($referer);
        }

        if ($isDatatable || ($action == 'archive' || $action == 'delete')) {
            return redirect($entityTypes);
            // when viewing individual entity
        }

        if ($ids !== []) {
            return redirect($entityTypes . '/' . $ids[0] . '/edit');
        }

        return redirect($entityTypes);
    }

    protected function downloadResponse(string $filename, $contents, string $type = 'application/pdf'): void
    {
        header('Content-Type: ' . $type);
        header('Content-Length: ' . mb_strlen($contents));

        if ( ! request()->debug) {
            header('Content-disposition: attachment; filename="' . $filename . '"');
        }

        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        echo $contents;

        exit;
    }
}
