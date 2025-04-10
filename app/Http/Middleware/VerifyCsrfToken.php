<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/stripe-zen', // このルートのCSRF検証を除外
        '/webhook/form', // CSRFチェックを除外するルート
        '/webhook/stripe', // CSRFチェックを除外するルート
        '/create-checkout-session/*',
        'webhook/robot-payment',

    ];
}
