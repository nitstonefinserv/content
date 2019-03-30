<?php
namespace Reflexions\Content\Models;

use Config;
use Storage;

class File extends \Eloquent
{
    protected $fillable = [
        'name',
        'mime',
        'size',
        'path',
        'url',
        'description',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function fileGroups()
    {
        return $this->belongsToMany(FileGroup::class, 'file_file_groups', 'file_id', 'file_group_id');
    }

    public function thumbnail( $name )
    {
        return $this->thumbnails()
            ->where('name', $name)
            ->first();
    }

    public function thumbnails()
    {
        return $this->hasMany(Thumbnail::class);
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

    /**
     * Return max upload size in formatted string
     */
    public static function uploadMax()
    {
        return static::formatFileSize(static::uploadMaxBytes());
    }

    // http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    public static function uploadMaxBytes()
    {
        static $max_size = -1;

        if( $max_size < 0 )
        {
            // Start with post_max_size.
            $max_size = static::parseSize(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = static::parseSize(ini_get('upload_max_filesize'));
            if( $upload_max > 0 && $upload_max < $max_size )
            {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    public static function parseSize( $size )
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if( $unit )
        {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else
        {
            return round($size);
        }
    }
    // end http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size

    // https://laracasts.com/forum/?p=1765-convert-bytes-to-gigabytes/0
    public static function formatFileSize( $size )
    {
        $units = [
            ' B',
            ' KB',
            ' MB',
            ' GB',
            ' TB',
        ];
        for( $i = 0; $size > 1024; $i++ )
        {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }
    // end https://laracasts.com/forum/?p=1765-convert-bytes-to-gigabytes/0
}
