<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * ReadVersionTask
 *
 * Reads the version number from a version.txt file.
 */
class ReadVersionTask extends BuildTask
{
	/**
	 * Property for File
	 *
	 * @var PhingFile file
	 */
	protected $file;

	/**
	 * Property to be set
	 *
	 * @var string $property
	 */
	protected $property;

	/**
	 * Main-Method for the Task
	 *
	 * @throws  BuildException
	 */
	public function main()
	{
		$this->assertProperty("file", "file");
		$this->assertProperty("property", "string");

		// get new version
		$version = $this->getVersion($this->file);

		// publish new version number as property
		$this->project->setProperty($this->property, $version);
	}

	/**
	 * Set Property for File containing version formation
	 *
	 * @param PhingFile $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * Set name of property to be set
	 *
	 * @param string $property
	 *
	 * @return void
	 */
	public function setProperty($property)
	{
		$this->property = $property;
	}

	/**
	 * Returns the version number from a file. Assumes the last line contains
	 * the version number.
	 *
	 * @param string $filename
	 *
	 * @return string
	 */
	private function getVersion($filename)
	{
		$this->log("Reading version: {$filename}");

		$contents = trim(file_get_contents($filename));
		$lines = explode("\n", $contents);
		$version = trim(array_pop($lines));

		$this->log("Version: {$version}");

		return $version;
	}
}