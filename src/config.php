<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Allowed image types
    |--------------------------------------------------------------------------
    |
    | List of allowed image extentions. Must be in lower case without '.'
    | This value can be overridden when calling the function.
    |
    */
    'mime_types' => [
        'image/png',
        'image/jpeg',
        'image/gif'
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Original image name
    |--------------------------------------------------------------------------
    |
    | If set to true then system will not generate pretty file name
    | using salmanbe/Filename library.
    |
    */
    'original_name' => false,
    
    /*
    |--------------------------------------------------------------------------
    | Default maximum image upload size in MB
    |--------------------------------------------------------------------------
    |
    | Define default maximum image upload size.
    | This option should never be empty and size must be in MB.
    |
    */
    'max_image_size' => 10,
    
    /*
    |--------------------------------------------------------------------------
    | Default resize type
    |--------------------------------------------------------------------------
    |
    | Default resize type applies to whole project
    | Possible values  'canvas', 'crop', 'resize', 'original', 'center', 'fit'
    | This value can be overridden when calling the function.
    |
    */
    'resize_type' => 'canvas',
    
    /*
    |--------------------------------------------------------------------------
    | Default transparent image path for water mark
    |--------------------------------------------------------------------------
    |
    | Path to default transparent image path for water mark.
    | This value can be overridden when calling the function.
    |
    */
    'water_mark_image' => public_path('assets/transparant.png'),
    
    /*
    |--------------------------------------------------------------------------
    | Default position of water mark image
    |--------------------------------------------------------------------------
    |
    | The position of water mark.
    | This value can be overridden when calling the function.
    |
    */
    'water_mark_position' => 'top-center',
];