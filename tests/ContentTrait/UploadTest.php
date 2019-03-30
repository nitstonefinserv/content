<?php namespace Reflexions\Content\Tests\ContentTrait;

use Exception;
use Image;
use Mockery;
use Reflexions\Content\Models\File;
use Reflexions\Content\Tests\TestCase;
use Storage;


class UploadTest extends TestCase
{
    protected function _mockUpload($original_name, $properties=array())
    {
        $properties = array_merge([
            'getClientOriginalName'      => $original_name,
            'getClientOriginalExtension' => 'jpg',
            'getMimeType' => 'image/jpeg',
            'getClientSize' => 1234,
            'getRealPath' => __DIR__.'/brussels.jpg',
            'getPathname' => __DIR__.'/brussels.jpg',
            'isValid' => true
        ], $properties);
        return Mockery::mock('\Symfony\Component\HttpFoundation\File\UploadedFile', $properties);
    }

    public static function getDiskName()
    {
        return Config::get('content.test-upload-disk', Config::get('content.upload-disk'));
    }

    public static function getDisk()
    {
        $diskPreference = static::getDiskName();
        return Storage::disk($diskPreference);
    }

    public function testFromUpload()
    {
        $model = new TestBench();
        $model->save();

        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');

        $group = 'Blog Images';
        $relative_path_prefix = '2015/01/01';

        $disk = static::getDisk();

        $file = $model->addFile(
            $upload,
            $relative_path_prefix,
            $group
        );
        $this->assertInternalType('integer', $file->id);
        $this->assertEquals('image-1.jpg', $file->name);
        $this->assertEquals('image/jpeg', $file->mime);
        $this->assertEquals(1234, $file->size);
        $this->assertEquals('2015/01/01/image-1.jpg', $file->path);
        $this->assertEquals($file->path, $file->url);
        $this->assertEquals(file_get_contents(__DIR__.'/brussels.jpg'), $disk->get($file->path));
        $this->assertEquals('Blog Images', $file->group_name);
        $this->assertEquals('blog-images', $file->group_slug);
        $this->assertEquals($model->id, $file->content_id);
    }


    public function testMinimal()
    {
        $model = new TestBench();
        $model->save();

        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg', [
            'getClientOriginalExtension' => 'jpg',
            'getMimeType' => null,
            'getClientSize' => null,
            'getRealPath' => __DIR__.'/brussels.jpg',
            'getPathname' => __DIR__.'/brussels.jpg',
            'isValid' => true
        ]);
        $file = $model->addFile($upload);

        $this->assertInternalType('integer', $file->id);
        $this->assertEquals('image-1.jpg', $file->name);
        $this->assertEquals(null, $file->mime);
        $this->assertEquals(null, $file->size);
        $this->assertEquals(null, $file->path);
        $this->assertEquals(null, $file->url);
        $this->assertEquals($model->id, $file->content_id);
    }

    public function testLeadImage()
    {
        $model = new TestBench();
        $model->save();

        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file = $model->addFile($upload, null, 'http://localhost/', null);

        $model->lead_image = $file;
        $model->save();

        $this->assertEquals('http://localhost/image-1.jpg', $model->lead_image->url);
    }

    public function testFileGroups()
    {
        $model = new TestBench();
        $model->save();
        
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, null, 'http://localhost/', 'Group1');
        
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-2.jpg');
        $file2 = $model->addFile($upload, null, 'http://localhost/', 'Group1');
        
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-3.jpg');
        $file3 = $model->addFile($upload, null, 'http://localhost/', 'Group2');

        $expected = [
            [ 'name' => 'image-1.jpg' ],
            [ 'name' => 'image-2.jpg' ],
        ];
        $this->assertArraySubset(
            $expected,
            $model->getFiles('Group1')->toArray()
        );
        $this->assertArraySubset(
            $expected,
            $model->getGroup('Group1')->files()->get()->toArray()
        );
        $this->assertArraySubset(
            [ 'name' => 'image-1.jpg' ],
            $model->getFile('Group1')->toArray()
        );

        $expected = [ [ 'name' => 'image-3.jpg' ] ];
        $this->assertArraySubset(
            $expected,
            $model->getFiles('Group2')->toArray()
        );
        $this->assertArraySubset(
            $expected,
            $model->getGroup('Group2')->files()->get()->toArray()
        );
        $this->assertArraySubset(
            [ 'name' => 'image-3.jpg' ],
            $model->getFile('Group2')->toArray()
        );

        $group3 = $model->getGroup('Group3');
        $this->assertArraySubset(
            ['slug' => 'group3'],
            $group3->toArray()
        );
        $this->assertEquals([], $group3->files()->get()->toArray());
        $this->assertEquals([], $model->getFiles('Group3')->toArray());
        $this->assertEquals(null, $model->getFile('Group3'));

        $group3->files()->attach($file1->id);
        $this->assertArraySubset(
            [],
            $model->micro_content->files()->where('group_slug', 'Group3')->get()->toArray()
        );
        $this->assertArraySubset(
            [ [ 'name' => 'image-1.jpg' ] ],
            $group3->files()->get()->toArray()
        );
        $this->assertArraySubset(
            [ [ 'name' => 'image-1.jpg' ] ],
            $model->getFiles('Group3')->toArray()
        );

        $expected = [
            [ 'name' => 'image-1.jpg' ],
            [ 'name' => 'image-2.jpg' ],
        ];
        $this->assertArraySubset(
            $expected,
            $model->getFiles('Group1')->toArray()
        );
        $this->assertArraySubset(
            $expected,
            $model->getGroup('Group1')->files()->get()->toArray()
        );
    }

    public function testFileGroupSetSequence()
    {
        $model = new TestBench();
        $model->save();
        
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, null, 'http://localhost/');
        $upload = $this->_mockUpload('c:/evil/../directory/image-2.jpg');
        $file2 = $model->addFile($upload, null, 'http://localhost/');
        
        $this->assertArraySubset(
            [
                [ 'name' => 'image-1.jpg' ],
                [ 'name' => 'image-2.jpg' ],
            ],
            $model->getGroup()->files()->get()->toArray()
        );

        $model->getGroup()->setSequence([$file2->id, $file1->id]);
        
        $this->assertArraySubset(
            [
                [ 'name' => 'image-2.jpg' ],
                [ 'name' => 'image-1.jpg' ],
            ],
            $model->getGroup()->files()->get()->toArray()
        );

        // remove sequence
        $model->getGroup()->setSequence(null);
        $this->assertEquals([], $model->getFiles()->toArray());
        $model->getGroup()->setSequence([]);
        $this->assertEquals([], $model->getFiles()->toArray());
        $model->getGroup()->setSequence(['']);
        $this->assertEquals([], $model->getFiles()->toArray());
    }

    public function testFileGroupSetLead()
    {
        $model = new TestBench();
        $model->save();
        
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, null, 'http://localhost/');
        $upload = $this->_mockUpload('c:/evil/../directory/image-2.jpg');
        $file2 = $model->addFile($upload, null, 'http://localhost/');
        
        $expectedBeforeSetLead = [
            [ 'name' => 'image-1.jpg' ],
            [ 'name' => 'image-2.jpg' ],
        ];
        $this->assertArraySubset(
            $expectedBeforeSetLead,
            $model->getGroup()->files()->get()->toArray()
        );
        $this->assertArraySubset(
            $expectedBeforeSetLead,
            $model->getFiles()->toArray()
        );

        $model->getGroup()->setLead($file2->id);
        
        $expectedAfterSetLead = [
            [ 'name' => 'image-2.jpg' ],
        ];
        $this->assertArraySubset(
            $expectedAfterSetLead,
            $model->getGroup()->files()->get()->toArray()
        );
        $this->assertArraySubset(
            $expectedAfterSetLead,
            $model->getFiles()->toArray()
        );
        $this->assertArraySubset(
            [ 'name' => 'image-2.jpg' ],
            $model->getFile()->toArray()
        );

        // but the files still exist
        $this->assertArraySubset(
            $expectedBeforeSetLead,
            $model->micro_content->files()->get()->toArray()
        );

        // remove lead
        $model->getGroup()->setLead('');
        $this->assertEquals([], $model->getFiles()->toArray());
        $this->assertEquals(null, $model->getFile());
    }

    public function testImageGalleryFieldDoesntThrowException()
    {
        $model = new TestBench();
        $model->save();

        $options = [
            'relative_path_prefix' => '/something',
        ];
        $fieldset = $model->getImageGalleryField('Group Name', $options);
        foreach($fieldset->getViews() as $view) {
            $temp = (string) $view;
        }

        $this->assertEquals(true, true);
    }

    public function testFileListFieldDoesntThrowException()
    {
        $model = new TestBench();
        $model->save();

        $options = [
            'relative_path_prefix' => '/something',
        ];
        $fieldset = $model->getFileListField('Group Name', $options);
        foreach($fieldset->getViews() as $view) {
            $temp = (string) $view;
        }

        $this->assertEquals(true, true);
    }

    public function testImageFieldDoesntThrowException()
    {
        $model = new TestBench();
        $model->save();

        $options = [
            'relative_path_prefix' => '/something',
        ];
        $fieldset = $model->getImageField('Group Name', $options);
        foreach($fieldset->getViews() as $view) {
            $temp = (string) $view;
        }

        $this->assertEquals(true, true);
    }

    public function testUniqueNames()
    {
        $model = new TestBench();
        $model->save();
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, '/path/', 'http://localhost/', 'Group1');
        
        $upload = $this->_mockUpload('c:/evil/../directory/image-1.jpg');
        $file2 = $model->addFile($upload, '/path/', 'http://localhost/', 'Group1');
        
        $expected = [
            [ 'name' => 'image-1.jpg' ],
            [ 'name' => 'image-2.jpg' ],
        ];
        $this->assertArraySubset(
            $expected,
            $model->getFiles('Group1')->toArray()
        );
        $this->assertArraySubset(
            $expected,
            $model->getGroup('Group1')->files()->get()->toArray()
        );
    }

    public function testMissingThumbnail()
    {
        $model = new TestBench();
        $model->save();
        
        $upload = $this->_mockUpload('c:/something/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, '/path/', 'http://localhost/', 'Group1');
        $thumbnail = $file1->thumbnail('square');
        $this->assertEquals(null, $thumbnail);
    }

    public function testThumbnails()
    {
        $this->app->config->set('content.thumbnail-presets', [
            'square' => [ 'width' => 50, 'height' => 50, 'encoding' => 'png' ],
        ]);

        $model = new TestBench();
        $model->save();
        
        $upload = $this->_mockUpload('c:/something/../directory/image-1.jpg');
        $file1 = $model->addFile($upload, '/path/', 'http://localhost/', 'Group1');
        $this->assertEquals(1, $file1->thumbnails()->count());

        $thumbnail = $file1->thumbnail('square');

        $this->assertEquals(
            'http://localhost/image-1.square.png',
            $thumbnail->url
        );
        $this->assertEquals(
            '/path/image-1.square.png',
            $thumbnail->path
        );

        $disk = static::getDisk();

        $image = Image::make($disk->get($thumbnail->path));
        $this->assertEquals(50, $image->width());
        $this->assertEquals(50, $image->height());


        $this->app->config->set('content.thumbnail-presets', [
            'square' => [ 'width' => 50, 'height' => 50, 'encoding' => 'jpg' ],
        ]);

        $model2 = new TestBench();
        $model2->save();
        
        $upload = $this->_mockUpload('c:/something/../directory/image-2.jpg');
        $file2 = $model2->addFile($upload, '/path/', 'http://localhost/', 'Group1');
        $this->assertEquals(1, $file2->thumbnails()->count());

        $thumbnail2 = $file2->thumbnail('square');

        $this->assertEquals(
            'http://localhost/image-2.square.jpg',
            $thumbnail2->url
        );
        $this->assertEquals(
            '/path/image-2.square.jpg',
            $thumbnail2->path
        );
        $image = Image::make($disk->get($thumbnail2->path));
        $this->assertEquals(50, $image->width());
        $this->assertEquals(50, $image->height());
    }
}
