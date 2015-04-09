<?php

namespace GemsPhing\Version;

use Underscore\_;
use GemsPhing\GemsAssert;
use GemsPhing\GemsFile;
use GemsPhing\GemsLog;

/**
 * Class Version
 *
 * @package GemsPhing\Version
 */
class GemsVersion
{
	/**
	 * Reads the version number from a string
	 *
	 * @param string $text
	 *
	 * @return array
	 * @throws \BuildException
	 */
	public static function get($text)
	{
		$lines = GemsFile::toArray($text);

		// read the last line
		$version = trim(array_pop($lines));
		GemsAssert::notEmpty($version, "Last line of version file is empty.");
		GemsLog::log("Version: {$version}");

		// read the build number
		$numbers = explode('.', $version);
		GemsAssert::areEqual(count($numbers), 3, "Bad version format. Must be major.minor.build format.");
		GemsAssert::isTruthy(_::create($numbers)
							  ->all(function ($n)
							  {
								  return is_numeric($n);
							  }), "Bad version format. Must 3 numeric values.");

		return _::create($numbers)
				->map(function ($n)
				{
					return (int)$n;
				});
	}
}