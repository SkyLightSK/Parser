<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum number of items per category
    |--------------------------------------------------------------------------
    |
    | This number shows how many items will be parsed in one category.
    |
    */

    'per_run' => 64,

    /*
    |--------------------------------------------------------------------------
    | Maximum number of items that will be updated
    |--------------------------------------------------------------------------
    |
    | This number shows how many items will be updated in all parsed list.
    |
    */

    'update_per' => 12,


    /*
    |--------------------------------------------------------------------------
    | Category list
    |--------------------------------------------------------------------------
    |
    | This is a list of links to categories. Each category must have items for
    | parsing. The amount indicated in the option above will be parsed.
    |
    */

    'category_url' => [
        'https://rozetka.com.ua/ua/notebooks/c80004/filter/',
        'https://rozetka.com.ua/ua/mobile-phones/c80003/'
    ],

    /*
     |--------------------------------------------------------------------------
     | Item link selector
     |--------------------------------------------------------------------------
     |
     | This value indicates where you want to take the value of the item link
     |
     */

    'item-link' => '.g-i-tile-i-title a',

    /*
     |--------------------------------------------------------------------------
     | Item name selector
     |--------------------------------------------------------------------------
     |
     | This value indicates where you want to take the value of the item name
     |
     */

    'item-name' => '.detail-title',

    /*
     |--------------------------------------------------------------------------
     | Item description selector
     |--------------------------------------------------------------------------
     |
     | This value indicates where you want to take the value of the item description
     |
     */

    'item-desc' =>  '.short-description',


    /*
     |--------------------------------------------------------------------------
     | Item photo selector
     |--------------------------------------------------------------------------
     |
     | This value indicates where you want to take the value of the item photo
     |
     */

    'item-photo'    => '#base_image'
    
];