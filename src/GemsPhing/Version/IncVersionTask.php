<?php

namespace GemsPhing\Version;

use Belt\_;
use GemsPhing\GemsAssert;
use GemsPhing\GemsFile;
use GemsPhing\GemsTask;

/**
 * IncVersion
 *
 * Increments the build number.
 *
 * @package GemsPhing\Version
 */
class IncVersionTask extends GemsTask
{
	/**
	 * Property for File
	 */
	protected $file;

	/**
	 * Increases the last component of the array
	 *
	 * @param array $version
	 *
	 * @return string
	 */
	public static function inc(array $version)
	{
		GemsAssert::notEmpty($version, "Unexpected empty array.");

		$arr = _::create($version);

		return $arr->snip(1)
				   ->push($arr->pop() + 1)
				   ->join(".");
	}

	/**
	 * @throws  \BuildException
	 */
	public function main()
	{
		GemsAssert::noneEmptyString($this->file, "File parameter not set.");

		$text = GemsFile::read($this->file);
		$lines = GemsFile::toArray($text);

		$version = GemsVersion::get($lines);
		$str = self::inc($version);

		// replace last line with new version
		$text = _::create($lines)
				 ->snip(1)
				 ->push($str)
				 ->join(PHP_EOL);

		GemsFile::write($this->file, $text);
	}

	/**
	 * Set Property for File containing version formation
	 *
	 * @param string $file
	 */
	public function setFile($file)
	{
		GemsAssert::noneEmptyString($file, "Expecting a string.");
		$this->file = $file;
	}
}