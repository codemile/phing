<?php

class GemsTestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function _construct_1()
	{
		$task = new GemsPhing\GemsTask();
		$this->assertEmpty($task->params);

		return $task;
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage Chicken property does not exist.
	 */
	public function assertProperty_1(GemsPhing\GemsTask $task)
	{
		$task->assertProperty("Chicken", "int");
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage You must specify a TestNull property.
	 */
	public function assertProperty_2(GemsPhing\GemsTask $task)
	{
		$task->TestNull = null;
		$task->assertProperty("TestNull", "int");
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage The Value property must be an integer.
	 */
	public function assertProperty_3(GemsPhing\GemsTask $task)
	{
		$task->Value = "house";
		$task->assertProperty("Value", "int");
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage The Value property can not be an empty string.
	 */
	public function assertProperty_4(GemsPhing\GemsTask $task)
	{
		$task->Value = 123;
		$task->assertProperty("Value", "string");
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage File foobar.tmp does not exist.
	 */
	public function assertProperty_5(GemsPhing\GemsTask $task)
	{
		$task->Value = "foobar.tmp";
		$task->assertProperty("Value", "file");
	}

	/**
	 * @test
	 * @depends                       _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessageRegEx Supplied file .*foobar.tmp is empty\.
	 */
	public function assertProperty_6(GemsPhing\GemsTask $task)
	{
		$DS = DIRECTORY_SEPARATOR;
		$task->Value = realpath(dirname(__FILE__).$DS.'..'.$DS.'data'.$DS.'foobar.tmp');
		$task->assertProperty("Value", "file");
	}

	/**
	 * @test
	 * @depends                       _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessageRegEx .*bar is not a directory\.
	 */
	public function assertProperty_7(GemsPhing\GemsTask $task)
	{
		$DS = DIRECTORY_SEPARATOR;
		$task->Value = realpath(dirname(__FILE__).$DS.'..'.$DS.'data'.$DS.'bar');
		$task->assertProperty("Value", "dir");
	}

	/**
	 * @test
	 * @depends                       _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function assertProperty_8(GemsPhing\GemsTask $task)
	{
		$DS = DIRECTORY_SEPARATOR;
		$task->Value = realpath(dirname(__FILE__).$DS.'..'.$DS.'data'.$DS.'foo');
		$this->assertTrue($task->assertProperty("Value", "dir"));
	}

	/**
	 * @test
	 * @depends _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function getFiles_1(GemsPhing\GemsTask $task)
	{
	}

	/**
	 * @test
	 * @depends                  _construct_1
	 *
	 * @expectedException \BuildException
	 * @expectedExceptionMessage Task not implemented.
	 *
	 * @param \GemsPhing\GemsTask $task
	 *
	 * @throws BuildException
	 */
	public function main_1(GemsPhing\GemsTask $task)
	{
		$task->main();
	}

	/**
	 * @test
	 * @depends _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function shell_1(GemsPhing\GemsTask $task)
	{
	}

	/**
	 * @test
	 * @depends _construct_1
	 *
	 * @param \GemsPhing\GemsTask $task
	 */
	public function toggle_1(GemsPhing\GemsTask $task)
	{
	}
}