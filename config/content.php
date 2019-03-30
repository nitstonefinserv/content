<?php

/**
 *
 * Reflexions Content
 *
 */

return [

    /*
    |--------------------------------------------------------------------------
    | admin-layout
    |--------------------------------------------------------------------------
    |
    | Customize the admin layout by extending content::admin.layout
    | and updating the admin-layout setting below.
    |
    */
   	'admin-layout' => 'content::admin.layout',

    /*
    |--------------------------------------------------------------------------
    | upload-disk
    |--------------------------------------------------------------------------
    |
    | Name of filesystem disk (from filesystems.php) to be used for content 
    | file and image uploads.
    | 
    |
    */
    'upload-disk' => env('UPLOAD_DISK', 'upload'),

    /*
    |--------------------------------------------------------------------------
    | thumbnail presets
    |--------------------------------------------------------------------------
    |
    | Generate thumbnails for uploaded images by defining named presets below.
    | 
    |
    */
    'thumbnail-presets' => [
        // 'landscape' => [ 'width' => 747, 'height' => 475, 'encoding' => 'jpg' ],
        // 'square' => [ 'width' => 475, 'height' => 475, 'encoding' => 'jpg' ],
        // 'hero' => [ 'width' => 1920, 'height' => 600, 'encoding' => 'jpg' ],
    ],

    /*
    |--------------------------------------------------------------------------
    | permissions
    |--------------------------------------------------------------------------
    |
    | Permissions for User Roles
    |
    */
    'permissions' => [
        // 'create' => 'Create',
        // 'read' => 'Read',
        // 'update' => 'Update',
        // 'delete' => 'Delete'
    ]

];

