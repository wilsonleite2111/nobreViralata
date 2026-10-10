<?php

$proxies = env('TRUSTED_PROXIES');

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Behind a proxy or CDN such as Cloudflare, the visitor's real address
    | arrives in forwarding headers. Those headers are only believed when
    | the request comes from a proxy listed here; otherwise anyone could
    | fake their address and slip past the rate limits. Use "*" only when
    | the server cannot be reached except through the proxy.
    |
    */

    'proxies' => match (true) {
        blank($proxies) => null,
        $proxies === '*' => '*',
        default => array_map(trim(...), explode(',', $proxies)),
    },

];
