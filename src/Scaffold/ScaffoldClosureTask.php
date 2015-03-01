<?php

require_once __DIR__."/ScaffoldComponentsTask.php";

/**
 * Handles the creation of the _All.js and _Package.js files.
 */
class ScaffoldClosureTask extends ScaffoldComponentsTask
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
			"/** Do not edit. This file was auto-created **/",
			sprintf('goog.provide("%s.All");', $name),
			sprintf('goog.require("%s");', $name),
			''
		);

		self::eachDirectory($dir, function ($folder) use ($name, &$lines)
		{
			$lines[] = sprintf('goog.require("%s.%s.All");', $name, $folder);
		});

		self::eachComponent($dir, function ($component) use ($name, &$lines)
		{
			$lines[] = sprintf('goog.require("%s.%s");', $name, $component);
		});

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