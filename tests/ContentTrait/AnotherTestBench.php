<?php namespace Reflexions\Content\Tests\ContentTrait;

use Reflexions\Content\Traits\ContentTrait;

class AnotherTestBench extends \Eloquent {
	use ContentTrait;
	protected $table = 'anothertestbench';
}