<?php
namespace Reflexions\Content\Models;

use Storage;
use Config;

class Thumbnail extends \Eloquent
{
    protected $fillable = [
        'name',
        'path',
        'url',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function delete($delete_from_storage = true)
    {
        if( !empty($this->path) && $delete_from_storage)
        {
            $diskPreference = Config::get('content.upload-disk');
            $disk = Storage::disk($diskPreference);
            $disk->delete($this->path);
        }
        parent::delete();
    }
}