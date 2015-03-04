<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * IncVersion
 *
 * Increments the build number.
 */
class IncVersionTask extends BuildTask
{
	/**
	 * Property for File
	 */
	protected $file;

	/**
	 * Main-Method for the Task
	 *
	 * @throws  BuildException
	 */
	public function main()
	{
		$this->assertProperty("file", "file");

		$this->log("Reading version: {$this->file}");

		// read the last line
		$contents = trim(file_get_contents($this->file));
		$lines = explode("\n", $contents);
		$version = trim(array_pop($lines));

		$this->log("Version: {$version}");

		// increase the build number
		list($major, $minor, $build) = explode('.', $version);
		$build++;
		$version = "$major.$minor.$build";
		$this->log("Next Version: {$version}");

		// save the changes
		$lines[] = $version;
		file_put_contents($this->file, implode("\n", $lines));
	}

	/**
	 * Set Property for File containing version formation
	 *
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}
}