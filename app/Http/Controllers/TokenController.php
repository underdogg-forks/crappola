<?php

namespace App\Http\Controllers;

use App\Models\AccountToken;
use App\Services\TokenService;

/**
 * Class TokenController.
 */
class TokenController extends BaseController
{
    protected \App\Services\TokenService $tokenService;

    /**
     * TokenController constructor.
     *
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {
        //parent::__construct();

        $this->tokenService = $tokenService;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable()
    {
        return $this->tokenService->getDatatable(\Illuminate\Support\Facades\Auth::user()->id);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $publicId)
    {
        $token = AccountToken::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
            ->where('public_id', '=', $publicId)->firstOrFail();

        $data = [
            'token'  => $token,
            'method' => 'PUT',
            'url'    => 'tokens/' . $publicId,
            'title'  => trans('texts.edit_token'),
        ];

        return \Illuminate\Support\Facades\View::make('accounts.token', $data);
    }

    /**
     * @param $publicId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($publicId)
    {
        return $this->save($publicId);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
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

        return \Illuminate\Support\Facades\View::make('accounts.token', $data);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulk()
    {
        $action = \Illuminate\Support\Facades\Request::input('bulk_action');
        $ids = \Illuminate\Support\Facades\Request::input('bulk_public_id');
        $count = $this->tokenService->bulk($ids, $action);

        \Illuminate\Support\Facades\Session::flash('message', trans('texts.archived_token'));

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }

    /**
     * @param bool $tokenPublicId
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function save($tokenPublicId = false)
    {
        if (\Illuminate\Support\Facades\Auth::user()->account->hasFeature(FEATURE_API)) {
            $rules = [
                'name' => 'required',
            ];

            if ($tokenPublicId) {
                $token = AccountToken::where('account_id', '=', \Illuminate\Support\Facades\Auth::user()->account_id)
                    ->where('public_id', '=', $tokenPublicId)->firstOrFail();
            }

            $validator = \Illuminate\Support\Facades\Validator::make(\Illuminate\Support\Facades\Request::all(), $rules);

            if ($validator->fails()) {
                return \Illuminate\Support\Facades\Redirect::to($tokenPublicId ? 'tokens/edit' : 'tokens/create')->withInput()->withErrors($validator);
            }

            if ($tokenPublicId) {
                $token->name = trim(\Illuminate\Support\Facades\Request::input('name'));
            } else {
                $token = AccountToken::createNew();
                $token->name = trim(\Illuminate\Support\Facades\Request::input('name'));
                $token->token = mb_strtolower(\Illuminate\Support\Str::random(RANDOM_KEY_LENGTH));
            }

            $token->save();

            if ($tokenPublicId) {
                $message = trans('texts.updated_token');
            } else {
                $message = trans('texts.created_token');
            }

            \Illuminate\Support\Facades\Session::flash('message', $message);
        }

        return \Illuminate\Support\Facades\Redirect::to('settings/' . ACCOUNT_API_TOKENS);
    }
}
