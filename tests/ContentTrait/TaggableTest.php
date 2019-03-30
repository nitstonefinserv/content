<?php namespace Reflexions\Content\Tests\ContentTrait;

use Carbon\Carbon;
use Reflexions\Content\Models\Tag;
use Reflexions\Content\Models\Status;
use Reflexions\Content\Tests\TestCase;
use Content;

class TaggableTest extends TestCase
{
	public function testAddTag()
	{
		$model = TestBench::create();
		$model->addTag('First');
		$model->addTag('Second');
		$model->addTag('Third');

		$this->assertEquals(
			['first' => 'First', 'second' => 'Second', 'third' => 'Third'],
			$model->getTagNames()
		);
		$this->assertEquals(
			['first' => 'First', 'second' => 'Second', 'third' => 'Third'],
			$model->tag_names
		);
		
		$tags = $model->getTags();
		$this->assertInstanceOf(Tag::class, $tags[0]);
		$this->assertEquals('First', $tags[0]->name);
		$this->assertEquals('first', $tags[0]->slug);
		$this->assertInternalType('integer', $tags[0]->id);

		$model = TestBench::create();
		$model->addTag(['First', 'Second', 'Third']);
		$this->assertSame(
			['first' => 'First', 'second' => 'Second', 'third' => 'Third'],
			$model->getTagNames()
		);
	}

	public function testAddTagOnce()
	{
		$model = TestBench::create();
		$model->addTag('First');
		$model->addTag('First');
		$model->addTag('First');
		$this->assertEquals(
			['first' => 'First'],
			$model->getTagNames()
		);
	}
	
	public function testRemoveTag()
	{
		$model = TestBench::create();
		$model->addTag('One');
		$model->addTag('Two');
		$model->addTag('Three');
		$model->removeTag('Two');
		$this->assertSame(['one' => 'One', 'three' => 'Three'], $model->getTagNames());
		$model->removeTag('one');
		$this->assertSame(['three' => 'Three'], $model->getTagNames());
	}
	public function testReplaceTags()
	{
		$model = TestBench::create();
		
		$model->addTag('First');
		$model->addTag('Second');
		
		$model->replaceTags(['Foo', 'Bar']);
		$this->assertEquals(['foo' => 'Foo', 'bar' => 'Bar'], $model->getTagNames());
		
		$model->replaceTags([]);
		$this->assertEquals([], $model->getTagNames());
	}

	public function testAddTagWithVocabulary()
	{
		$model = TestBench::create();
		$model->addTag('First');
		$model->addTag('Second');
		$model->addTag('Third');
		$this->assertEquals(
			['first' => 'First', 'second' => 'Second', 'third' => 'Third'],
			$model->getTagNames()
		);

		$model->addTag('Foo', 'Another Vocabulary');
		$model->addTag('Bar', 'Another Vocabulary');
		$this->assertEquals(
			['first' => 'First', 'second' => 'Second', 'third' => 'Third'],
			$model->getTagNames()
		);
		$this->assertEquals(
			['foo' => 'Foo', 'bar' => 'Bar'],
			$model->getTagNames('Another Vocabulary')
		);
		$this->assertEquals(
			['foo' => 'Foo', 'bar' => 'Bar'],
			$model->getTagNames('another-vocabulary')
		);
	}

	public function testFreshlyAddedTagIsPublished()
	{
		$now = Carbon::now();
		$model = TestBench::create();
		$model->addTag('First');
		$tag = $model->tags()->first();
		$this->assertEquals(Status::PUBLISHED, $tag->publish_status);
		$this->assertGreaterThanOrEqual($now, $tag->publish_date);
	}

	public function testLookupTerms()
	{
		$model = TestBench::create();
		$model->addTag('First');
		$model = TestBench::create();
		$model->addTag('Second');
		$model = TestBench::create();
		$model->addTag('Second Tag');
		$model = TestBench::create();
		$model->addTag('Third');
		$model = TestBench::create();
		$model->addTag('Foo', 'Another Vocabulary');
		$model = TestBench::create();
		$model->addTag('Bar', 'Another Vocabulary');
		$model = TestBench::create();
		$model->addTag('Bar Again', 'Another Vocabulary');
		
		$this->assertEquals(
			['Second', 'Second Tag'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('s')->get()->toArray() )
		);
		$this->assertEquals(
			['First'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('f')->get()->toArray() )
		);
		$this->assertEquals(
			['Third'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('t')->get()->toArray() )
		);
		$this->assertEquals(
			['Foo', 'Bar', 'Bar Again'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('', 'Another Vocabulary')->get()->toArray() )
		);
		$this->assertEquals(
			['Foo'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('f', 'Another Vocabulary')->get()->toArray() )
		);
		$this->assertEquals(
			['Foo'],
			array_map(function($t) { return $t['name']; }, Tag::byPrefixAndGroup('f', 'another-vocabulary')->get()->toArray() )
		);
	}

	public function testTagsWithSameSlug()
	{
		$model = TestBench::create();
		$model->addTag('Foo');
		$model = TestBench::create();
		$model->addTag('Foo', 'Another Vocabulary');
	}

	public function testAddingSameTag()
	{
		$model = TestBench::create();
		$model->addTag('Foo');
		$model->addTag('Foo');
		$this->assertEquals(
			1,
			$model->micro_content->tags()->count()
		);
	}

	public function testQueryScope()
	{
		$model1 = TestBench::create();
		$model1->addTag('Foo');

		$model2 = TestBench::create();
		$model2->addTag('Foo');
		$model2->addTag('Bar');

		$model3 = TestBench::create();
		$model3->addTag('Bar');

		$this->assertEquals(
			[$model1->id, $model2->id],
			TestBench::ofTags('Foo')
				->orderBy('id')->get()->map(function($model) {
					return $model->id;
				})
				->toArray()
		);
		$this->assertEquals(
			[$model2->id, $model3->id],
			TestBench::ofTags('Bar')
				->orderBy('id')->get()->map(function($model) {
					return $model->id;
				})
				->toArray()
		);
		$this->assertEquals(
			[$model1->id, $model2->id, $model3->id],
			TestBench::ofTags(['Foo', 'Bar'])
				->orderBy('id')->get()->map(function($model) {
					return $model->id;
				})
				->toArray()
		);
		$this->assertEquals(
			[$model2->id],
			TestBench::ofTags('Foo')
				->ofTags('Bar')
				->orderBy('id')->get()->map(function($model) {
					return $model->id;
				})
				->toArray()
		);
	}
}