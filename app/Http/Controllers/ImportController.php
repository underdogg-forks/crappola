<?php

namespace App\Http\Controllers;

use App\Jobs\ImportData;
use App\Services\ImportService;
use Exception;
use Illuminate\Http\Request;
use Utils;

class ImportController extends BaseController
{
    public $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function doImport(Request $request)
    {
        if ( ! \Illuminate\Support\Facades\Auth::user()->confirmed) {
            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withError(trans('texts.confirm_account_to_import'));
        }

        $source = \Illuminate\Support\Facades\Request::input('source');
        $files = [];
        $timestamp = time();

        foreach (ImportService::$entityTypes as $entityType) {
            $fileName = $entityType;
            if ($request->hasFile($fileName)) {
                $file = $request->file($fileName);
                $destinationPath = env('FILE_IMPORT_PATH') ?: storage_path() . '/import';
                $extension = mb_strtolower($file->getClientOriginalExtension());

                if ($source === IMPORT_CSV) {
                    if ($extension !== 'csv') {
                        return redirect()->to('/settings/' . ACCOUNT_IMPORT_EXPORT)->withError(trans('texts.invalid_file'));
                    }
                } elseif ($source === IMPORT_JSON) {
                    if ($extension !== 'json') {
                        return redirect()->to('/settings/' . ACCOUNT_IMPORT_EXPORT)->withError(trans('texts.invalid_file'));
                    }
                } elseif ( ! in_array($extension, ['csv', 'xls', 'xlsx', 'json'])) {
                    return redirect()->to('/settings/' . ACCOUNT_IMPORT_EXPORT)->withError(trans('texts.invalid_file'));
                }

                $newFileName = sprintf('%s_%s_%s.%s', \Illuminate\Support\Facades\Auth::user()->account_id, $timestamp, $fileName, $extension);
                $file->move($destinationPath, $newFileName);
                $files[$entityType] = $destinationPath . '/' . $newFileName;
            }
        }

        if ($files === []) {
            \Illuminate\Support\Facades\Session::flash('error', trans('texts.select_file'));

            return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }

        try {
            if ($source === IMPORT_CSV) {
                $data = $this->importService->mapCSV($files);

                return \Illuminate\Support\Facades\View::make('accounts.import_map', [
                    'data'      => $data,
                    'timestamp' => $timestamp,
                ]);
            }

            if ($source === IMPORT_JSON) {
                $includeData = filter_var(\Illuminate\Support\Facades\Request::input('data'), FILTER_VALIDATE_BOOLEAN);
                $includeSettings = filter_var(\Illuminate\Support\Facades\Request::input('settings'), FILTER_VALIDATE_BOOLEAN);
                if (config('queue.default') === 'sync') {
                    $results = $this->importService->importJSON($files[IMPORT_JSON], $includeData, $includeSettings);
                    $message = $this->importService->presentResults($results, $includeSettings);
                } else {
                    $settings = [
                        'files'            => $files,
                        'include_data'     => $includeData,
                        'include_settings' => $includeSettings,
                    ];
                    $this->dispatch(new ImportData(\Illuminate\Support\Facades\Auth::user(), IMPORT_JSON, $settings));
                    $message = trans('texts.import_started');
                }
            } elseif (config('queue.default') === 'sync') {
                $results = $this->importService->importFiles($source, $files);
                $message = $this->importService->presentResults($results);
            } else {
                $settings = [
                    'files'  => $files,
                    'source' => $source,
                ];
                $this->dispatch(new ImportData(\Illuminate\Support\Facades\Auth::user(), false, $settings));
                $message = trans('texts.import_started');
            }

            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withWarning($message);
        } catch (Exception $exception) {
            Utils::logError($exception);
            \Illuminate\Support\Facades\Session::flash('error', $exception->getMessage());

            return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }
    }

    public function doImportCSV()
    {
        try {
            $map = \Illuminate\Support\Facades\Request::input('map');
            $headers = \Illuminate\Support\Facades\Request::input('headers');
            $timestamp = \Illuminate\Support\Facades\Request::input('timestamp');

            if (config('queue.default') === 'sync') {
                $results = $this->importService->importCSV($map, $headers, $timestamp);
                $message = $this->importService->presentResults($results);
            } else {
                $settings = [
                    'timestamp' => $timestamp,
                    'map'       => $map,
                    'headers'   => $headers,
                ];
                $this->dispatch(new ImportData(\Illuminate\Support\Facades\Auth::user(), IMPORT_CSV, $settings));
                $message = trans('texts.import_started');
            }

            return redirect('/settings/' . ACCOUNT_IMPORT_EXPORT)->withWarning($message);
        } catch (Exception $exception) {
            Utils::logError($exception);
            \Illuminate\Support\Facades\Session::flash('error', $exception->getMessage());

            return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
        }
    }

    public function cancelImport()
    {
        try {
            $path = env('FILE_IMPORT_PATH') ?: storage_path() . '/import';
            foreach ([ENTITY_CLIENT, ENTITY_INVOICE, ENTITY_PAYMENT, ENTITY_QUOTE, ENTITY_PRODUCT] as $entityType) {
                $fileName = sprintf('%s/%s_%s_%s.csv', $path, \Illuminate\Support\Facades\Auth::user()->account_id, request()->timestamp, $entityType);
                \Illuminate\Support\Facades\File::delete($fileName);
            }
        } catch (Exception $exception) {
            Utils::logError($exception);
        }

        return \Illuminate\Support\Facades\Redirect::to('/settings/' . ACCOUNT_IMPORT_EXPORT);
    }
}
