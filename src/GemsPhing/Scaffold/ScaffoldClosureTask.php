<?php

use GemsPhing\GemsString;
use GemsPhing\Scaffold\AbstractScaffoldTask;

/**
 * @readme Tasks ScaffoldClosureTask
 *
 * Handles the creation of the _All.js and _Package.js files.
 */
class ScaffoldClosureTask extends AbstractScaffoldTask
{
	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updateComponentFolder($dir, $package, array $parts)
	{
		// ignored
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updatePackageFolder($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;
		$jsAll = $dir.$DS."_All.js";
		$jsPackage = $dir.$DS."_Package.js";

		$this->writeFile($jsAll, $this->getAll($dir, $package, $parts), true);
		$this->writeFile($jsPackage, $this->getPackage($package, $parts));
	}

	/**
	 * Adds a blank line if the last element is not blank.
	 *
	 * @param array $lines
	 *
	 * @return array
	 */
	private static function space(array $lines)
	{
		if(end($lines) != '')
		{
			$lines[] = '';
		}
		return $lines;
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 *
	 * @return array
	 */
	private function getAll($dir, $package, array $parts)
	{
		$name = empty($parts)
			? $package
			: sprintf("%s.%s", implode(".", $parts), $package);

		$lines = array(
			'//***********************************************',
			'//* Do not edit. This file will be auto-updated *',
			'//***********************************************',
			'',
			sprintf('goog.provide("%s.All");', $name),
			sprintf('goog.require("%s");', $name),
			''
		);

		self::eachDirectory($dir, function ($folder) use ($name, &$lines)
		{
			$lines[] = sprintf('goog.require("%s.%s.All");', $name, $folder);
		});
		$lines = self::space($lines);

		self::eachComponent($dir, function ($component) use ($name, &$lines)
		{
			$lines[] = sprintf('goog.require("%s.%s");', $name, $component);
		});
		$lines = self::space($lines);

		self::eachFile($dir, function ($file) use ($name, &$lines)
		{
			if(GemsString::endsWith($file,".js")
				&& !GemsString::startsWith($file,"_")
				&& !GemsString::endsWith($file,".Test.js"))
			{
				$lines[] = sprintf('goog.require("%s.%s");', $name, substr($file,0,-3));
			}
		});
		$lines = self::space($lines);

		return $lines;
	}

	/**
	 * @param string $package
	 * @param array  $parts
	 *
	 * @return array
	 */
	private function getPackage($package, array $parts)
	{
		$namespace = implode(".", $parts);

		return array(
			sprintf('goog.provide("%s.%s");', $namespace, $package),
			sprintf('goog.require("%s");', $namespace)
		);
	}
}