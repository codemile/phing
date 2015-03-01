<?php

require_once __DIR__."/DirTreeTask.php";

/**
 * Class ScaffoldComponentsTask
 */
abstract class ScaffoldComponentsTask extends DirTreeTask
{
	/**
	 * @var string Directories that start with this string will be handled as component folders.
	 */
	const PREFIX = 'cg';

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	abstract function updateComponentFolder($dir, $package, array $parts);

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	abstract function updatePackageFolder($dir, $package, array $parts);

	/**
	 * Each sub-directory that is a component.
	 *
	 * @param string           $dir
	 * @param callable(string) $func
	 */
	protected static function eachComponent($dir, $func)
	{
		$DS = DIRECTORY_SEPARATOR;
		$files = scandir($dir);
		asort($files);
		foreach ($files as $file)
		{
			if (!BuildTask::startsWith($file, self::PREFIX) || !is_dir($dir.$DS.$file))
			{
				continue;
			}
			$func($file);
		}
	}

	/**
	 * @param string $dir
	 * @param callback(string) $func
	 */
	protected static function eachFile($dir, $func)
	{
		$DS = DIRECTORY_SEPARATOR;
		$files = scandir($dir);
		asort($files);
		foreach($files as $file)
		{
			if(!is_file($dir.$DS.$file))
			{
				continue;
			}
			$func($file);
		}
	}

	/**
	 * Each sub-directory that is not use to define a component.
	 *
	 * @param string           $dir
	 * @param callable(string) $func
	 */
	protected static function eachDirectory($dir, $func)
	{
		$DS = DIRECTORY_SEPARATOR;
		$files = scandir($dir);
		asort($files);
		foreach ($files as $file)
		{
			if (BuildTask::startsWith($file,'.') || BuildTask::startsWith($file, self::PREFIX) || is_file($dir.$DS.$file))
			{
				continue;
			}
			$func($file);
		}
	}

	/**
	 * Ensures that a component folder contains all of it's required files.
	 *
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	protected function directory($dir, $package, array $parts)
	{
		if (BuildTask::startsWith($package, self::PREFIX) && $package != 'cgTag')
		{
			$this->updateComponentFolder($dir, $package, $parts);

			return;
		}
		$this->updatePackageFolder($dir, $package, $parts);
	}

	/**
	 * @param string $file
	 * @param array  $lines
	 * @param bool   $overwrite
	 */
	protected function writeFile($file, array $lines, $overwrite = false)
	{
		if (!$overwrite && file_exists($file))
		{
			return;
		}

		$this->log($file);

		$contents = implode(PHP_EOL, $lines).PHP_EOL;
		$previous = file_exists($file)
			? file_get_contents($file)
			: '';
		if ($previous != $contents)
		{
			file_put_contents($file, $contents);
		}
	}
}