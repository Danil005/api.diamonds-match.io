<?php

function generateNumbers(): array
{
    $numbers = [];
    for ($i = 0; $i < 101; $i++) $numbers[] = $i;
    return $numbers;
}

return [

    /*
    |--------------------------------------------------------------------------
    | Глобальные переменные для обработки анкет
    |--------------------------------------------------------------------------
    |
    | Пример body

    |
    */
    'questionnaire' => [
        'fields' => [
            'partner_appearance' => 'partner_appearance',
            'personal_qualities_partner' => 'personal_qualities_partner',
            'partner_information' => 'partner_information',
            'test' => 'test',
            'my_appearance' => 'my_appearance',
            'my_personal_qualities' => 'my_personal_qualities',
            'my_information' => 'my_information',
        ],
        'value' => [
            'partner_appearance' => [
                'sex' => ['female', 'male'],
                'ethnicity' => [
                    'no_matter', 'caucasoid', 'asian', 'dark_skinned',
                    'hispanic', 'indian', 'native_middle_east', 'mestizo',
                    'native_american', 'islands', 'other'
                ],
                'body_type' => ['any', 'athletic', 'slim', 'hourglass', 'full'],
                'chest' => ['!required', 'any', 'big', 'middle', 'small'],
                'booty' => ['!required', 'any', 'big', 'middle', 'small'],
                'hair_color' => ['any', 'brunette', 'blonde', 'redhead', 'brown-haired'],
                'hair_length' => ['!required', 'any', 'short', 'long'],
                'eye_color' => ['any', 'blue', 'gray', 'green', 'brown']
            ],
            'personal_qualities_partner' => [
                ['calm', 'energetic', null],
                ['live_in_moment', 'pragmatic', null],
                ['ambitious', 'modest', null],
                ['self', 'need_support', null],
                ['housewifely', 'indifferent_life', null],
                ['aristocratic', 'simple', null],
                ['sport', 'indifferent_sport', null],
                ['lover_going_out', 'home', null],
                ['adventuress', 'rational', null],
                ['strong-willed', 'soft', null],
                ['lark', 'owl', null],
                ['humanitarian', 'mathematical', null],
                ['open', 'cautious', null],
                ['extrovert', 'introvert', null],
                ['infantile', 'mature', null],
            ],
            'partner_information' => [
                'age' => ['type:integer', 'type:integer'],
                'place_birth' => 'array(string)',
                'live_place' => 'array(string)',
                'zodiac_signs' => [
                    'aries', 'calf', 'twins', 'cancer', 'lion', 'virgo',
                    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius',
                    'fish'
                ],
                'height' => ['type:integer|double', 'type:integer|double'],
                'weight' => ['type:integer|double', 'type:integer|double'],
                'marital_status' => ['one', 'divorced', 'widow'],
                'languages' => 'array(string|integer)',
                'moving_country' => 'boolean',
                'moving_city' => 'boolean',
                'children' => 'boolean',
                'children_count' => [...generateNumbers(), '!required'],
                'children_desire' => ['yes', 'no', 'maybe'],
                'smoking' => 'string',
                'alcohol' => 'string',
                'religion' => 'string',
                'sport' => 'string'
            ],
            'test' => [
                'lies' => [0, 1, 2, 3],
                'intervention' => [0, 1, 2],
                'value' => [0, 1, 2, 3, 4, 5, 6],
                'life' => [0, 1, 2],
                'motive_marriage' => [0, 1, 2],
                'family_atmosphere' => [0, 1, 2],
                'position_sex' => [0, 1, 2, 3],
                'books' => [0, 1, 2, 3],
                'friends' => [0, 1, 2],
                'leisure' => [0, 1, 2],
                'discussion_feelings' => [0, 1, 2],
                'work_relationship' => [0, 1, 2, 3],
                'family_decisions' => [0, 1, 2, 3],
                'consent' => [0, 1, 2],
                'interests_partner' => [0, 1, 2, 3],
                'first_place_relationship' => [0, 1, 2, 3, 4],
                'position_society' => [0, 1, 2, 3, 4, 5, 6],
                'conflicts' => [0, 1, 2, 3, 4],
                'cleanliness' => [0, 1, 2, 3],
                'clear_plan' => [0, 1],
                'conflict_behavior' => [0, 1, 2, 3],
            ],
            'my_appearance' => [
                'ethnicity' => [
                    'no_matter', 'caucasoid', 'asian', 'dark_skinned',
                    'hispanic', 'indian', 'native_middle_east', 'mestizo',
                    'native_american', 'islands', 'other'
                ],
                'body_type' => ['any', 'athletic', 'slim', 'hourglass', 'full'],
                'chest' => ['!required', 'any', 'big', 'middle', 'small'],
                'booty' => ['!required', 'any', 'big', 'middle', 'small'],
                'hair_color' => ['any', 'brunette', 'blonde', 'redhead', 'brown-haired'],
                'hair_length' => ['!required', 'any', 'short', 'long'],
                'eye_color' => ['any', 'blue', 'gray', 'green', 'brown']
            ],
            'my_personal_qualities' => [
                'calm' => 'boolean',
                'energetic' => 'boolean',
                'live_in_moment' => 'boolean',
                'pragmatic' => 'boolean',
                'ambitious' => 'boolean',
                'modest' => 'boolean',
                'self' => 'boolean',
                'need_support' => 'boolean',
                'housewifely' => 'boolean',
                'indifferent_life' => 'boolean',
                'aristocratic' => 'boolean',
                'simple' => 'boolean',
                'sport' => 'boolean',
                'indifferent_sport' => 'boolean',
                'lover_going_out' => 'boolean',
                'home' => 'boolean',
                'adventuress' => 'boolean',
                'rational' => 'boolean',
                'strong-willed' => 'boolean',
                'soft' => 'boolean',
                'lark' => 'boolean',
                'owl' => 'boolean',
                'humanitarian' => 'boolean',
                'mathematical' => 'boolean',
                'open' => 'boolean',
                'cautious' => 'boolean',
                'extrovert' => 'boolean',
                'introvert' => 'boolean',
                'infantile' => 'boolean',
                'mature' => 'boolean',
            ],
            'my_information' => [
                'name' => 'string',
                'place_birth' => 'string',
                'live_city' => 'string|integer',
                'live_country' => 'string|integer',
                'zodiac_signs' => [
                    'aries', 'calf', 'twins', 'cancer', 'lion', 'virgo',
                    'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius',
                    'fish'
                ],
                'height' => 'integer|double',
                'weight' => 'integer|double',
                'marital_status' => ['one', 'divorced', 'widow'],
                'languages' => 'array(string|integer)',
                'moving_country' => 'boolean',
                'moving_city' => 'boolean',
                'children' => 'boolean',
                'children_count' => [...generateNumbers(), '!required'],
                'children_desire' => ['yes', 'no', 'maybe'],
                'smoking' => 'string',
                'alcohol' => 'string',
                'religion' => 'string',
                'sport' => 'string',
                'education' => 'string',
                'work' => 'string',
                'salary' => 'string|double|integer',
                'health_problems' => 'string',
                'allergies' => 'string',
                'pets' => 'string',
                'have_pets' => 'string',
                'films_or_books' => 'string',
                'relax' => 'string',
                'countries_was' => 'string',
                'countries_dream' => 'string',
                'best_gift' => 'string',
                'hobbies' => 'string',
                'kredo' => 'string',
                'features_repel' => 'string',
                'age_difference' => 'string|integer|double',
                'films' => 'string',
                'songs' => 'string',
                'ideal_weekend' => 'string',
                'sleep' => 'string',
                'doing_10' => 'string',
                'signature_dish' => 'string',
                'clubs' => 'string',
                'best_gift_received' => 'string',
                'talents' => 'string'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool)env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'ru',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'ru',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'ru_RU',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        Intervention\Image\ImageServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Image' => Intervention\Image\Facades\Image::class
    ],

];
