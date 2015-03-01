<?php

require_once __DIR__."/ScaffoldComponentsTask.php";

class ScaffoldSASSTask extends ScaffoldComponentsTask
{
	/**
	 * @var array Components that end with one of these strings will not have SASS files.
	 */
	private static $tails = ['Controller', 'Service', 'Factory', 'Filter', 'Provider', 'Config'];

	/**
	 * Ensures a SASS file exists in each component folder.
	 *
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updateComponentFolder($dir, $package, array $parts)
	{
		if (!self::createSASS($package))
		{
			return;
		}
		$DS = DIRECTORY_SEPARATOR;
		$sass = $dir.$DS.$package.".scss";
		$this->writeFile($sass, $this->getComponent($package, $parts));
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updatePackageFolder($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;
		$sassAll = $dir.$DS."_All.scss";
		$sassPackage = $dir.$DS."_Package.scss";
		$this->writeFile($sassAll, $this->getAll($dir), true);
		$this->writeFile($sassPackage, $this->getPackage($dir));
	}

	/**
	 * @param string $package
	 *
	 * @return bool
	 */
	private static function createSASS($package)
	{
		foreach (self::$tails as $tail)
		{
			if (BuildTask::endsWith($package, $tail))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @param string $dir
	 *
	 * @return array
	 */
	private function getAll($dir)
	{
		$DS = DIRECTORY_SEPARATOR;
		$lines = [
			'/***********************************************',
			' * Do not edit. This file will be auto-updated *',
			' ***********************************************/',
			''
		];

		self::eachDirectory($dir, function ($folder) use ($dir, $DS, &$lines)
		{
			if (file_exists($dir.$DS.$folder.$DS."_All.scss"))
			{
				$lines[] = sprintf('@import "%s/All";', $folder);
			}
		});

		self::eachComponent($dir, function ($component) use ($dir, $DS, &$lines)
		{
			if (file_exists($dir.$DS.$component.$DS.$component.".scss"))
			{
				$lines[] = sprintf('@import "%s/%s";', $component, $component);
			}
		});

		$lines[] = '';
		$lines[] = '@import "Package";';
		$lines[] = '';

		return $lines;
	}

	/**
	 * @param string $dir
	 *
	 * @return array
	 */
	private function getPackage($dir)
	{
		$DS = DIRECTORY_SEPARATOR;
		$lines = [
			'/** Import package wide styles here. **/',
			''
		];

		self::eachFile($dir, function ($file) use ($dir, $DS, &$lines)
		{
			if(!BuildTask::endsWith($file,".scss") || $file == '_All.scss' || $file == '_Package.scss')
			{
				return;
			}
			$lines[] = sprintf('@import "%s";', str_replace('.scss', '', $file));
		});

		return $lines;
	}

	/**
	 * @param string $package
	 * @param array  $parts
	 *
	 * @returns string
	 */
	private function getComponent($package, array $parts)
	{
		$import = implode("/", array_fill(0, count($parts) - 1, ".."));

		return array(
			"@import \"{$import}/Config\";",
			"",
			".{$package} {",
			"}"
		);
	}
}