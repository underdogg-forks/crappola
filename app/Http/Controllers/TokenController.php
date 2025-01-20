<?php

namespace App\Http\Controllers;

use App\Models\AccountToken;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

/**
 * Class TokenController.
 */
class TokenController extends BaseController
{
    protected TokenService $tokenService;

    /**
     * TokenController constructor.
     */
    public function __construct(TokenService $tokenService)
    {
        //parent::__construct();

        $this->tokenService = $tokenService;
    }

    /**
     * @return RedirectResponse
     */
    public function index()
    {
        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    public function getDatatable()
    {
        return $this->tokenService->getDatatable(Auth::user()->id);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($publicId)
    {
        $token = AccountToken::where('company_id', '=', Auth::user()->company_id)
            ->where('public_id', '=', $publicId)->firstOrFail();

        $data = [
            'token'  => $token,
            'method' => 'PUT',
            'url'    => 'tokens/' . $publicId,
            'title'  => trans('texts.edit_token'),
        ];

        return View::make('companies.token', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @param bool $tokenPublicId
     *
     * @return $this|RedirectResponse
     */
    public function save($tokenPublicId = false)
    {
        if (Auth::user()->company->hasFeature(FEATURE_API)) {
            $rules = [
                'name' => 'required',
            ];

            if ($tokenPublicId) {
                $token = AccountToken::where('company_id', '=', Auth::user()->company_id)
                    ->where('public_id', '=', $tokenPublicId)->firstOrFail();
            }

            $validator = Validator::make(\Request::all(), $rules);

            if ($validator->fails()) {
                return Redirect::to($tokenPublicId ? 'tokens/edit' : 'tokens/create')->withInput()->withErrors($validator);
            }

            if ($tokenPublicId) {
                $token->name = trim($request->get('name'));
            } else {
                $token = AccountToken::createNew();
                $token->name = trim($request->get('name'));
                $token->token = strtolower(str_random(RANDOM_KEY_LENGTH));
            }

            $token->save();

            $message = $tokenPublicId ? trans('texts.updated_token') : trans('texts.created_token');

            Session::flash('message', $message);
        }

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    /**
     * @return RedirectResponse
     */
    public function store()
    {
        return $this->save();
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $data = [
            'token'  => null,
            'method' => 'POST',
            'url'    => 'tokens',
            'title'  => trans('texts.add_token'),
        ];

        return View::make('companies.token', $data);
    }

    /**
     * @return RedirectResponse
     */
    public function bulk()
    {
        $action = $request->get('bulk_action');
        $ids = $request->get('bulk_public_id');
        $count = $this->tokenService->bulk($ids, $action);

        Session::flash('message', trans('texts.archived_token'));

        return Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }
}
