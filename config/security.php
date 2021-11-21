<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Evil configuration
    |--------------------------------------------------------------------------
    |
    | This defines the evil attributes and tags, which will always be stripped
    | from the input.
    |
    */

    'evil' => [
        'attributes' => null,
        'tags'       => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Replacement string
    |--------------------------------------------------------------------------
    |
    | This defines the replacement string, which will be used to take the place
    | of removed portions of strings where XSS was present.
    |
    */

    'replacement' => null,

];
