<?php namespace Reflexions\Content\Tests\ContentTrait;

use Reflexions\Content\Traits\ContentTrait;

class TestBench extends \Eloquent {
	use ContentTrait;
	protected $table = 'testbench';
	public $fillable = ['text'];
}