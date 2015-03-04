<?php

class BuildTaskTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function endsWith()
	{
		$this->assertTrue(GemsPhing\BuildTask::endsWith("Spaceship","ship"));
	}
}