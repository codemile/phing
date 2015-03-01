<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * BuildPlugin
 *
 * Handles the building of a plugin.
 */
class BuildPluginTask extends BuildTask
{
	/**
	 * Property for Dir
	 *
	 * @var PhingFile file
	 */
	protected $dir;

	/**
	 * Property for output
	 */
	protected $output;

	/**
	 * Main-Method for the Task
	 *
	 * @return  void
	 * @throws  BuildException
	 */
	public function main()
	{
		// check supplied attributes
		$this->assertProperty("dir", "dir");
		$this->assertProperty("output", "dir");

		$dir = realpath($this->dir);
		$output = realpath($this->output);

		$this->log("Building plugin project.");
		$this->log("Plugin: $dir");
		$this->log("Output: $output");

		$where = getcwd();
		chdir($dir);
		$this->shell("phing -Dgems.dir.dist={$output}");
		chdir($where);
	}

	/**
	 * Set Property for Dir containing the update SQL files.
	 *
	 * @param PhingFile $dir
	 */
	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * Set Property for output
	 *
	 * @param string $value
	 */
	public function setOutput($value)
	{
		$this->output = $value;
	}
}