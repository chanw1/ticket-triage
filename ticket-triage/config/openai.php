<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | This key is pulled from your .env file. Make sure you have
    | OPENAI_API_KEY=sk-... set in .env
    |
    */

    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default organization (optional)
    |--------------------------------------------------------------------------
    */
    'organization' => env('OPENAI_ORGANIZATION', null),

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable classification
    |--------------------------------------------------------------------------
    */
    'enabled' => env('OPENAI_CLASSIFY_ENABLED', false),

];