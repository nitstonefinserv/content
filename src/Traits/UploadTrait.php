<?php namespace Reflexions\Content\Traits;

use Config;
use Content;
use Illuminate\Support\Facades\Input;
use Image;
use Reflexions\Content\Admin\AdminOptions;
use Reflexions\Content\Admin\Form\FieldSet;
use Reflexions\Content\Models\File;
use Reflexions\Content\Models\FileGroup;
use Reflexions\Content\Models\Thumbnail;
use Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use View;

/**
 * Factored for readability rather than reusability
 * @see \Reflexions\Content\ContentTrait
 */
trait UploadTrait
{
    public static function getDiskName()
    {
        return AdminOptions::getDiskName();
    }

    public static function getDisk()
    {
        return AdminOptions::getDisk();
    }

    /**
     * Mutators to support $instance->lead_image
     */
    public function getLeadImageAttribute()
    {
        return $this->content->leadImage;
    }

    /**
     * Set the lead image of this model to the provided File
     */
    public function setLeadImageAttribute( File $value )
    {
        if( empty($value) )
        {
            throw new \Exception('Invalid lead image value');
        }
        return $this->content->lead_image_id = $value->id;
    }


    /**
     * Associate Upload with this content item
     *
     * @param UploadedFile $upload
     * @param string $relative_path_prefix This path is relative to the laravel disk, and prepended to the filename.
     * @param string $group_name
     * @return File
     * @throws \Exception
     */
    public function addFile( UploadedFile $upload, $relative_path_prefix = null, $group_name = 'default' )
    {
        if( !$upload->isValid() || empty($upload->getPathname()) )
        {
            throw new \Exception("Invalid Upload file");
        }

        // make sure there's a trailing /
        $relative_path_prefix = preg_replace('{/$}', '', $relative_path_prefix) . '/';

        if( $group_name )
        {
            $group_name = trim($group_name);
        }

        $file = new File();
        $file->content_id = $this->content->id;
        $file->filename = static::uniqueFilename($relative_path_prefix, $upload->getClientOriginalName());
        $file->name = $file->filename;
        $file->mime = $upload->getMimeType();
        $file->size = $upload->getClientSize();

        // We store full paths & urls, but that's problemmatic if we want to e.g. move to a cdn (url changes)
        // or are importing a db from production to a staging env, which would then show images from production.
        // Instead, we should switch to using paths relative to the selected filesystem
        $file->path = $relative_path_prefix . $file->name;
        $file->url = AdminOptions::getUrlPrefix() . $file->path;

        if( $group_name )
        {
            $file->group_name = $group_name;
            $file->group_slug = str_slug($group_name);
        }

        $file->save();

        if( $group_name )
        {
            $this->addFileToGroup($file, $group_name);
        }

        $disk = static::getDisk();

        $disk->put(
            $file->path,
            file_get_contents($upload->getRealPath())
        );

        $is_image = false;
        try {
            $is_image = is_array(getimagesize($upload->getRealPath()));
        }
        catch (\Exception $e) {}

        if ($is_image)
        {
            foreach( Config::get('content.thumbnail-presets') as $preset_name => $settings )
            {
                $thumbnail = new Thumbnail();
                $thumbnail->name = $preset_name;
                $thumbnail->file_id = $file->id;
                $thumbnail->path = $relative_path_prefix . 'thumbs/' . pathinfo($file->filename, PATHINFO_FILENAME) . '.' . $preset_name . '.' . $settings['encoding'];
                $thumbnail->url = AdminOptions::getUrlPrefix() . $thumbnail->path;
                $disk->put(
                    $thumbnail->path,
                    Image::make($upload->getRealPath())
                        ->fit($settings['width'], $settings['height'])
                        ->encode($settings['encoding'])
                        ->getEncoded()
                );
                $thumbnail->save();
            }
        }

        return $file;
    }

    /**
     * Adds a file reference to the named group.
     * The file doesn't need to actually be uploaded
     * on the given group.
     */
    public function addFileToGroup( $file, $group_name )
    {
        $group = $this->getGroup($group_name);
        if( empty($group) )
        {
            $group = new FileGroup();
            $group->slug = str_slug($group_name);
            $group->content_id = $this->content->id;
            $group->save();
        }

        $group->files()->attach($file);
    }

    /**
     * Return uploads associated with this content item
     */
    public function getFiles( $group_name_or_slug = 'default' )
    {
        $group = $this->getGroup($group_name_or_slug);
        if( $group )
        {
            return $group->files()->get();
        }
        else
        {
            return [];
        }
    }

    /**
     * Return single upload associated with this content item
     */
    public function getFile( $group_name_or_slug = 'default' )
    {
        $group = $this->getGroup($group_name_or_slug);
        if( $group )
        {
            return $group->files()->first();
        }
        else
        {
            return null;
        }
    }


    /**
     * Mutators to support $instance->lead_image_id
     */
    public function getLeadImageIdAttribute()
    {
        return $this->content->lead_image_id;
    }

    public function setLeadImageIdAttribute( $value )
    {
        if( empty($value) )
        {
            $value = null;
        }
        $this->content->lead_image_id = $value;
    }

    public function getGroup( $group_name_or_slug = 'default' )
    {
        $slug = str_slug($group_name_or_slug);
        $group = $this
            ->content
            ->fileGroups()
            ->where('slug', $slug)
            ->orderBy('id')
            ->first();
        if( empty($group) )
        {
            $group = new FileGroup();
            $group->content_id = $this->content->id;
            $group->slug = $slug;
            $group->save();
        }
        return $group;
    }

    /**
     * Provides an Image Gallery component
     */
    public function getImageGalleryField( $group_name, $options )
    {
        $content = $this->content;
        $relative_path_prefix = $options['relative_path_prefix'];
        $upload_url_prefix = $options['upload_url_prefix'];
        $upload_max = \Reflexions\Content\Models\File::uploadMax();
        $upload_max_bytes = \Reflexions\Content\Models\File::uploadMaxBytes();
        $images = $this->getFiles($group_name);
        $attribute = str_slug($group_name);
        $options = array_merge($options, ['sizing' => 'col-sm-9']);
        $label = $group_name;
        $view = View::make(
            Content::package() . '::contenttrait.admin.components.image_gallery',
            compact('content', 'relative_path_prefix', 'upload_url_prefix', 'upload_max', 'upload_max_bytes', 'images', 'attribute', 'label', 'options')
        );

        return new FieldSet(
            [],
            [],
            [$view],
            [],
            [
                $attribute => function ( $attribute, $model )
                {
                    $ids = explode(',', Input::get($attribute));
                    $group = $model->getGroup($attribute);
                    if( $group )
                    {
                        $group->setSequence($ids);
                    }
                },
            ]
        );
    }

    /**
     * Provides a File List component
     */
    public function getFileListField( $group_name, $options )
    {
        $content = $this->content;
        $relative_path_prefix = $options['relative_path_prefix'];
        $upload_url_prefix = $options['upload_url_prefix'];
        $upload_max = \Reflexions\Content\Models\File::uploadMax();
        $upload_max_bytes = \Reflexions\Content\Models\File::uploadMaxBytes();
        $files = $this->getFiles($group_name);
        $attribute = str_slug($group_name);
        $options = array_merge($options, ['sizing' => 'col-sm-9']);
        $label = $group_name;
        $view = View::make(
            Content::package() . '::contenttrait.admin.components.file_list',
            compact('content', 'relative_path_prefix', 'upload_url_prefix', 'upload_max', 'upload_max_bytes', 'files', 'attribute', 'label', 'options')
        );

        return new FieldSet(
            [],
            [],
            [$view],
            [],
            [
                $attribute => function ( $attribute, $model )
                {
                    $ids = explode(',', Input::get($attribute));
                    $group = $model->getGroup($attribute);
                    if( $group )
                    {
                        $group->setSequence($ids);
                    }
                },
            ]
        );
    }

    public function getImageField( $group_name, $options )
    {
        $content = $this->content;
        $relative_path_prefix = $options['relative_path_prefix'];
        $upload_url_prefix = $options['upload_url_prefix'];
        $upload_max = \Reflexions\Content\Models\File::uploadMax();
        $upload_max_bytes = \Reflexions\Content\Models\File::uploadMaxBytes();

        $current_image = $this->getFile($group_name);

        $attribute = str_slug($group_name);
        $options = array_merge($options, []);
        $image_help_text = isset($options['image_help_text']) ? $options['image_help_text'] : '';
        $label = $group_name;
        $view = View::make(
            Content::package() . '::contenttrait.admin.components.image',
            compact('content', 'relative_path_prefix', 'upload_url_prefix', 'upload_max', 'upload_max_bytes', 'current_image', 'attribute', 'label', 'image_help_text', 'options')
        );

        return new FieldSet(
            [],
            [],
            [$view],
            [],
            [
                $attribute => function ( $attribute, $model )
                {
                    $id = Input::get($attribute);
                    $group = $model->getGroup($attribute);
                    if( $group )
                    {
                        $group->setLead($id);
                    }
                },
            ]
        );
    }

    /**
     * Return a ckeditor component for a form
     * @param string $attribute name of attribute
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Reflexions\Content\Admin\Form\FieldSet
     */
    public function getCKEditorField( $attribute, $label, $options = [] )
    {
        $options = array_merge($options, \Reflexions\Content\Admin\Form::defaults($options));
        $content = $this->content;
        $relative_path_prefix = $options['relative_path_prefix'];
        $upload_url_prefix = $options['upload_url_prefix'];
        $upload_max = \Reflexions\Content\Models\File::uploadMax();
        $upload_max_bytes = \Reflexions\Content\Models\File::uploadMaxBytes();
        return new FieldSet(
            [$attribute],
            [$attribute => $options['validation']],
            [
                \View::make(
                    \Content::package() . '::contenttrait.admin.components.ckeditor',
                    compact('attribute', 'label', 'options', 'content', 'relative_path_prefix', 'upload_url_prefix', 'upload_max', 'upload_max_bytes')
                ),
            ],
            [],
            []
        );
    }

    /**
     * Return a unique filename given the provided prefix and filename
     */
    public static function uniqueFilename($relative_path_prefix, $file )
    {
        $file = basename($file);

        $disk = self::getDisk();

        if( $disk->exists($relative_path_prefix . $file) )
        {
            // Split filename into parts
            $pathInfo = pathinfo($file);
            $extension = isset($pathInfo['extension'])
                ? ('.' . $pathInfo['extension'])
                : '';

            // Look for a number before the extension; add one if there isn't already
            if( preg_match('/(.*?-)(\d+)$/', $pathInfo['filename'], $match) )
            {
                // Have a number; increment it
                $base = $match[1];
                $number = intVal($match[2]);
            }
            else
            {
                // No number; add one
                $base = $pathInfo['filename'] . '-';
                $number = 0;
            }

            // Choose a name with an incremented number until a file with that name
            // doesn't exist
            do
            {
                $file = $base . ++$number . $extension;
            } while( $disk->exists($relative_path_prefix . $file) );
        }
        return $file;
    }
}
