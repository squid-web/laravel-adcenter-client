<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AdCenter-URL
    |--------------------------------------------------------------------------
    |
    | Basis-URL van de AdCenter-server. Het loader-script wordt geladen vanaf
    | {url}/l.js. Een slash aan het eind wordt automatisch weggehaald.
    |
    */

    // Leeg (ADCENTER_URL=) telt als niet ingesteld
    'url' => env('ADCENTER_URL') ?: 'https://adcenter.squid-web.nl',

    /*
    |--------------------------------------------------------------------------
    | Sitecode
    |--------------------------------------------------------------------------
    |
    | De code van deze site zoals die in AdCenter is aangemaakt. Zonder
    | sitecode staat AdCenter uit en worden er geen advertenties geladen.
    |
    */

    'site' => env('ADCENTER_SITE'),

    /*
    |--------------------------------------------------------------------------
    | Ingeschakeld
    |--------------------------------------------------------------------------
    |
    | Zet op false om AdCenter (bijvoorbeeld lokaal of op staging) uit te
    | schakelen. Slots tonen dan alleen hun eigen fallback-inhoud.
    |
    */

    'enabled' => env('ADCENTER_ENABLED', true),

];
