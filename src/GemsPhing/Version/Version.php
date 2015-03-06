<?php

namespace GemsPhing\Version;

use GemsPhing\GemsAssert;
use GemsPhing\GemsLog;

/**
 * Class Version
 *
 * @package GemsPhing\Version
 */
class Version
{
	/**
	 * @var \Project
	 */
	private $_project;

	/**
	 * @var string[]
	 */
	private $_lines;

	/**
	 * @var int[]
	 */
	private $_version;

	/**
	 * @param string $filename
	 *
	 * @throws \BuildException
	 */
	public function __construct($filename)
	{
		GemsLog::log("Reading: {$filename}");

		$contents = trim(GemsAssert::read_file($filename));
		$this->_lines = explode("\n", $contents);

		if (empty($this->_lines))
		{
			throw new \BuildException("Version file is empty.");
		}

		$last = trim(array_pop($this->_lines));
		if (empty($last))
		{
			throw new \BuildException("Last line of version file is empty.");
		}
		$this->_project->log("Version: {$last}");

		$numbers = explode('.', $last);
		if (count($numbers) != 3)
		{
			throw new \BuildException("Bad version format. Must be major.minor.build format.");
		}

		$this->_version = __($numbers)->map(function ($n)
		{
			if (!is_numeric($n))
			{
				throw new \BuildException("Bad version format. Must be major.minor.build format.");
			}

			return (int)$n;
		});
	}
}