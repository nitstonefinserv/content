<?php namespace Reflexions\Content\Tests\ContentTrait;

use Carbon\Carbon;
use Reflexions\Content\Models\Content;
use Reflexions\Content\Models\Status;
use Reflexions\Content\Tests\TestCase;

class PublishableTest extends TestCase
{
	/**
	 * Test linked publishable instance
	 */
	public function testLinkedMicroContentInstance() {
		$model = new TestBench();
		$this->assertInstanceOf(Content::class, $model->micro_content);
		$this->assertNull($model->id);
		$this->assertNull($model->micro_content->id);
		$model->save();
		$this->assertInternalType('integer', $model->id);
		$this->assertInternalType('integer', $model->micro_content->id);
		$this->assertEquals($model->created_at, $model->micro_content->created_at);
		$this->assertEquals($model->updated_at, $model->micro_content->updated_at);
		$this->assertEquals($model->deleted_at, $model->micro_content->deleted_at);
	}
	/**
	 * Test linked publishable instance
	 */
	public function testLinkedContentInstance() {
		$model = new TestBench();
		$this->assertInstanceOf(Content::class, $model->content);
		$this->assertNull($model->id);
		$this->assertNull($model->content->id);
		$model->save();
		$this->assertInternalType('integer', $model->id);
		$this->assertInternalType('integer', $model->content->id);
		$this->assertEquals($model->created_at, $model->content->created_at);
		$this->assertEquals($model->updated_at, $model->content->updated_at);
		$this->assertEquals($model->deleted_at, $model->content->deleted_at);
	}

	/**
	 * Test Dynamic Scheduled Status
	 */
	public function testScheduledStatus()
	{
		$model = new TestBench();
		$this->assertEquals(Status::STUB, $model->publish_status);
		$model->publish_status = Status::PUBLISHED;
		$this->assertEquals(Status::PUBLISHED, $model->publish_status);
		$this->assertLessThanOrEqual($model->publish_date, Carbon::now());
		$model->publish_date = Carbon::now()->addWeek();
		$this->assertEquals(Status::SCHEDULED, $model->publish_status);
		$model->publish_date = Carbon::now()->subWeek();
		$this->assertEquals(Status::PUBLISHED, $model->publish_status);
	}

	/**
	 * Test Status Label
	 */
	public function testStatusLabel()
	{
		$model = new TestBench();
		$this->assertEquals('New', $model->publish_status_label);
		$model->publish_status = Status::PUBLISHED;
		$model->publish_date = Carbon::now()->addWeek();
		$this->assertEquals('Scheduled', $model->publish_status_label);
		$model->publish_date = Carbon::now()->subWeek();
		$this->assertEquals('Published', $model->publish_status_label);
	}

	/**
	 * Test status tests
	 */
	public function testStatusTests()
	{
		$model = new TestBench();
		$this->assertEquals($model->micro_content->publish_status, Status::STUB);
		$this->assertTrue($model->isStub());
		$this->assertFalse($model->isDraft());
		$this->assertFalse($model->isScheduled());
		$this->assertFalse($model->isPublished());
		$this->assertFalse($model->isArchived());

		$model->publish_status = Status::DRAFT;
		$this->assertEquals($model->micro_content->publish_status, Status::DRAFT);
		$this->assertFalse($model->isStub());
		$this->assertTrue($model->isDraft());
		$this->assertFalse($model->isScheduled());
		$this->assertFalse($model->isPublished());
		$this->assertFalse($model->isArchived());

		$model->publish_status = Status::SCHEDULED;
		$model->publish_date = Carbon::now()->addWeek();
		$this->assertEquals($model->micro_content->publish_status, Status::PUBLISHED);
		$this->assertFalse($model->isStub());
		$this->assertFalse($model->isDraft());
		$this->assertTrue($model->isScheduled());
		$this->assertFalse($model->isPublished());
		$this->assertFalse($model->isArchived());

		$model->publish_status = Status::PUBLISHED;
		$model->publish_date = Carbon::now()->subWeek();
		$this->assertEquals($model->micro_content->publish_status, Status::PUBLISHED);
		$this->assertFalse($model->isStub());
		$this->assertFalse($model->isDraft());
		$this->assertFalse($model->isScheduled());
		$this->assertTrue($model->isPublished());
		$this->assertFalse($model->isArchived());

		$model->publish_status = Status::ARCHIVED;
		$this->assertEquals($model->micro_content->publish_status, Status::ARCHIVED);
		$this->assertFalse($model->isStub());
		$this->assertFalse($model->isDraft());
		$this->assertFalse($model->isScheduled());
		$this->assertFalse($model->isPublished());
		$this->assertTrue($model->isArchived());
	}

	/**
	 * Test Query Builder helper
	 */
	public function testPublishedQueryScope()
	{
		$stub = new TestBench();
		$stub->save();
		
		$draft = new TestBench();
		$draft->publish_status = Status::DRAFT;
		$draft->save();
		
		$scheduled = new TestBench();
		$scheduled->publish_status = Status::PUBLISHED;
		$scheduled->publish_date = Carbon::now()->addWeek();
		$scheduled->save();

		$published = new TestBench();
		$published->publish_status = Status::PUBLISHED;
		$published->save();

		$archived = new TestBench();
		$archived->publish_status = Status::ARCHIVED;
		$archived->save();

		$ids = array_map(
			function ($e) { return $e['id']; },
			TestBench::published()->get()->toArray()
		);
		$this->assertEquals([4], $ids);

		$ids = array_map(
			function ($e) { return $e['id']; },
			TestBench::withContent()->published()->get()->toArray()
		);
		$this->assertEquals([4], $ids);
	}


	/**
	 * Test Query Builder helper
	 */
	public function testIdCollisionWherePublished()
	{
		$another = new AnotherTestBench();
		$another->save();

		$published = new TestBench();
		$published->publish_status = Status::PUBLISHED;
		$published->save();

		$ids = array_map(
			function ($e) { return $e['id']; },
			TestBench::published()->get()->toArray()
		);

		$this->assertEquals([1], $ids);
	}	
}