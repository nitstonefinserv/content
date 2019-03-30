<?php
namespace Reflexions\Content\Admin\Http\Controllers;


use Reflexions\Content\Admin\AdminOptions;
use Reflexions\Content\Models\Content;
use Reflexions\Content\Models\File;
use Reflexions\Content\Models\Slug;
use Reflexions\Content\Models\Tag;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class ContentTraitAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function postApiDeletePreviousSlug( Request $request )
    {
        $slug = Slug::find($request->request->get('id'));

        if( $slug )
        {
            $slug->delete();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }


    /**
     * Lookup existing terms
     */
    public function getApiTermLookup( Request $request, $group_slug )
    {
        $prefix = $request->query->get('term', '');
        $results = [];

        if( !empty($prefix) )
        {
            $results[] = [
                'id'   => str_slug($request->query('term')),
                'text' => $request->query('term'),
            ];
        }

        $tags = null;
        if( empty($prefix) )
        {
            $tags = Tag::byPrefixAndGroup($prefix, $group_slug)->orderBy('name')->limit(100)->get();
        }
        else
        {
            $tags = Tag::byPrefixAndGroup($prefix, $group_slug)->get();
        }

        if( $tags )
        {
            $tags = $tags->map(
                function ( $t )
                {
                    return [
                        'id'   => str_slug($t->name),
                        'text' => $t->name,
                    ];
                }
            );
            $results = array_merge($results, $tags->toArray());
        }

        return response()->json(['results' => $results]);
    }


    /**
     * Lookup images for a particular content item
     */
    public function getApiContentImagesLookup( $content_id )
    {
        $content = Content::find($content_id);

        return response()->json(
            [
                'lead_image' => $content->leadImage()->first(),
                'images'     => $content->files()->where('mime', 'LIKE', 'image/%')->get()->map(function (File $file) {
                    if (strpos($file->url, 'http') === 0) {
                        // absolute path
                        $file->src = $file->url;
                    }
                    else {
                        // url is relative to the content disk's base url
                        $file->src = AdminOptions::getUrlPrefix() . $file->path;
                    }
                    return $file;
                }),
            ]
        );
    }


    /**
     * Lookup files for a particular content item
     */
    public function getApiContentFilesLookup( $content_id )
    {
        $content = Content::find($content_id);

        return response()->json(
            [
                'files' => $content->files()->get(),
            ]
        );
    }


    /**
     * Lookup files for a particular content item
     */
    public function postApiContentFileUpload( Request $request, $content_id )
    {
        $content = Content::find($content_id);
        $model = $content->model()->first();
        $file = $model->addFile(
            $request->file('upload'),
            $request->request->get('relative_path_prefix'),
            $request->request->get('group')
        );

        return response()->json(
            [
                'file' => $file,
            ]
        );
    }


    /**
     * Delete a particular content item's file
     */
    public function postApiContentFileDelete( Request $request, $content_id )
    {
        $content = Content::find($content_id);
        $file = $content->files()->where('id', $request->request->get('file_id'))->first();

        // remove this file from the tables that reference it (fk violation otherwise)

        if ($content->lead_image_id == $file->id)
        {
            $content->lead_image_id = null;
            $content->save();
        }

        foreach( $file->fileGroups as $fileGroup )
        {
            $fileGroup->pivot->delete();
        }

        foreach( $file->thumbnails as $thumbnail )
        {
            $thumbnail->delete();
        }

        $file->delete();

        return response()->json([]);
    }


    /**
     * Update single attribute on file (x-editable hook)
     */
    public function postApiContentFileAttributeUpdate( Request $request, $content_id )
    {
        $content = Content::find($content_id);
        $id = $request->request->get('pk');
        $attribute = $request->request->get('name');
        $value = $request->request->get('value');

        if( empty($id) || empty($attribute) || empty($value) )
        {
            return response("Invalid Arguments", 400);
        }

        $file = $content->files()->where('id', $id)->first();
        if( empty($file) )
        {
            return response("Invalid Arguments", 400);
        }

        $file->$attribute = $value;
        try
        {
            $file->save();
        }
        catch( \Exception $e )
        {
            return response("Invalid Arguments", 400);
        }

        return response()->json(['file' => $file]);
    }


    /**
     * Provide a filebrowser to CKEditor
     */
    public function getCKEditorFileBrowser( Request $request, $content_id, $type )
    {
        return view('content::contenttrait.admin.ckeditor-file-browser', compact('type'));
    }
}
