<?php

namespace App;

use Illuminate\Foundation\Application as BaseApplication;

class Application extends BaseApplication
{
    /** Adds support for a `public_html` folder instead of the default `public`. */
    public function publicPath(): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public_html';
    }
}
