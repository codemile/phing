<?php

class GemsTestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function construct_1()
	{
		$task = new GemsPhing\GemsTask();
		$this->assertEmpty($task->params);

		return $task;
	}

	/**
	 * @test
	 * @depends construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 * @expectedException \BuildException
	 * @expectedExceptionMessage Chicken property does not exist.
	 */
	public function assertProperty_1(GemsPhing\GemsTask $task)
	{
		$task->assertProperty("Chicken","int");
	}

	/**
	 * @test
	 * @depends construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function getFiles_1(GemsPhing\GemsTask $task)
	{
	}

	/**
	 * @test
	 * @depends construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function shell_1(GemsPhing\GemsTask $task)
	{
	}

	/**
	 * @test
	 * @depends construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function toggle_1(GemsPhing\GemsTask $task)
	{
	}
}