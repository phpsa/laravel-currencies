<?php

namespace Phpsa\LaravelCurrencies\Tests\Stubs;

use Illuminate\Http\Request;

class ProductController
{
    public static $rules = [];

    public function __invoke(Request $request)
    {
        $request->validate(static::$rules);
    }
}
