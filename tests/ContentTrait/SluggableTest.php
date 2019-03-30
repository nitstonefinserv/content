<?php namespace Reflexions\Content\Tests\ContentTrait;

use Carbon\Carbon;
use Reflexions\Content\Models\MicroContent;
use Reflexions\Content\Models\Status;
use Reflexions\Content\Models\Slug;
use Reflexions\Content\Tests\TestCase;

class SluggableTest extends TestCase
{
	/**
	 * Test linked publishable instance
	 */
	public function testSlugAttribute()
	{
		$model = new TestBench();
		$this->assertNull($model->slug);

		$model->slug = 'slug';
		$model->save();
		$this->assertEquals('slug', $model->slug);

		$model2 = TestBench::findBySlug('slug');
		$this->assertEquals($model->id, $model2->id);
		$this->assertEquals($model->slug, $model2->slug);
	}

	public function testFindBySlug()
	{
		$model1= new TestBench();
		$model1->slug = 'slug1';
		$model1->save();

		$model2 = new TestBench();
		$model2->slug = 'slug2';
		$model2->save();

		$model3 = new TestBench();
		$model3->slug = 'slug3';
		$model3->save();

		$this->assertEquals(TestBench::findBySlug('slug2')->id, $model2->id);
	}

	public function testFindByHistoricalSlug()
	{
		$model1= new TestBench();
		$model1->slug = 'slug1';
		$model1->save();

		$model2 = new TestBench();
		$model2->slug = 'slug2';
		$model2->save();
		$model2->slug = 'slug2-new';
		$model2->save();

		$model3 = new TestBench();
		$model3->slug = 'slug3';
		$model3->save();

		$this->assertEquals(TestBench::findBySlug('slug2')->id, $model2->id);
	}

	public function testHistoricalSlug()
	{
		$model = new TestBench();
		$this->assertNull($model->slug);
		$model->slug = 'slug';
		$model->save();

		$model->slug = 'newslug';
		$model->save();

		$another = new TestBench();
		$another->slug = 'random';
		$another->save();

		$model2 = TestBench::findBySlug('slug');
		$this->assertEquals($model->id, $model2->id);
		$model3 = TestBench::findBySlug('newslug');
		$this->assertEquals($model->id, $model3->id);

		$model->slug = 'another-slug';
		$model->save();
		$this->assertEquals(['slug', 'newslug', 'another-slug'], $model->getAllSlugs());
		$model->removeSlug('another-slug');
		$this->assertEquals(['slug', 'newslug'], $model->getAllSlugs());
		$this->assertEquals('newslug', $model->slug);
		$model->removeSlug('newslug');
		$this->assertEquals(['slug'], $model->getAllSlugs());
		$this->assertEquals('slug', $model->slug);


		$model->slug = 'newslug';
		$model->save();
		$model->slug = 'another-slug';
		$model->save();
		$model->removeSlug('newslug');
		$this->assertEquals(['slug', 'another-slug'], $model->getAllSlugs());
		$this->assertEquals('another-slug', $model->slug);
	}

	public function testNoAttributeErrorOnExistingSlug()
	{
		$model = new TestBench();
		$model->slug = 'slug';
		$model->save();

		$model2 = new TestBench();
		$model2->slug = 'slug';
		//$this->expectException(\Exception::class);
		$model2->save();
	}

	public function testAttributeErrorOnInvalidFormat()
	{
		$this->expectException(\Exception::class);
		$model = new TestBench();
		$model->slug = 'Invalid Slug!';
	}

	public function testAttributeErrorOnLongSlug()
	{
		$this->expectException(\Exception::class);
		$model = new TestBench();
		$string = "this-is-valid-slug-material";
		$model->slug = str_repeat($string, 10);
	}

	public function testOnUnSavedObject()
	{
		$this->expectException(\Exception::class);
		$model = new TestBench();
		$model->removeSlug('non-existent-slug');
	}

	public function testNormalizeSlug()
	{
		$model = new TestBench();
		$model->slug = str_slug('Invalid Slug!');
		$model->save();
		$this->assertEquals('invalid-slug', $model->slug);
	}

	public function testFindSlug()
	{
		$model = new TestBench();
		$model->slug = TestBench::findUniqueSlug('Testing', TestBench::class, $model->id);
		$model->save();
		$this->assertEquals('testing', $model->slug);

		$model = new TestBench();
		$model->slug = TestBench::findUniqueSlug('Testing', TestBench::class, $model->id);
		$this->assertEquals('testing-2', $model->slug);
	}

	public function testAllowSlugsWithSlashes()
	{
		$wrong = new TestBench();
		$wrong->save();
		$model = new TestBench();
		$model->slug = 'education/scholarships-and-grants';
		$model->save();
		$this->assertEquals('education/scholarships-and-grants', $model->slug);

		$result = TestBench::findBySlug('education/scholarships-and-grants');
		$this->assertEquals($model->id, $result->id);
		$this->assertEquals($model->slug, $result->slug);
	}
}