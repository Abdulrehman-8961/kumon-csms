<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
   'insert-contract','update-contract','vendor_check_data','uploadContractAttachment','revertContractAttachment','uploadSSLAttachment','revertSSLAttachment','uploadNetworkAttachment'
    ];
}
