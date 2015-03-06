<?php

namespace GemsPhing\Version;

use GemsPhing\GemsAssert;
use GemsPhing\GemsTask;

/**
 * IncVersion
 *
 * Increments the build number.
 */
class IncVersionTask extends GemsTask
{
	/**
	 * Property for File
	 */
	protected $file;

	/**
	 * @param string[] $lines
	 *
	 * @return string[]
	 * @throws \BuildException
	 */
	public function inc(array $lines)
	{
		if (empty($lines))
		{
			throw new \BuildException("Version file is empty.");
		}

		$version = trim(array_pop($lines));
		if (empty($version))
		{
			throw new \BuildException("Last line of version file is empty.");
		}

		$this->log("Version: {$version}");

		// increase the build number
		$numbers = explode('.', $version);
		if (count($numbers) != 3)
		{
			throw new \BuildException("Bad version format. Must be major.minor.build format.");
		}
		foreach ($numbers as $num)
		{
			if (!is_numeric($num))
			{
				throw new \BuildException("Bad version format. Must be major.minor.build format.");
			}
		}

		$numbers[2] = ((int)$numbers[2]) + 1;

		$version = implode(".", $numbers);

		$this->log("Next Version: {$version}");

		// save the changes
		$lines[] = $version;

		return $lines;
	}

	/**
	 * Main-Method for the Task
	 *
	 * @throws  \BuildException
	 */
	public function main()
	{
		$contents = trim(GemsAssert::read_file($this->file));

		$this->log("Reading version: {$this->file}");

		$lines = explode("\n", $contents);
		$lines = $this->inc($lines);
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