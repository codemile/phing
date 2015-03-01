<?php

require_once __DIR__."/DirTreeTask.php";

/**
 * Class ScaffoldComponentsTask
 */
class ScaffoldComponentsTask extends DirTreeTask
{
	/**
	 * @var string Directories that start with this string will be handled as component folders.
	 */
	const PREFIX = 'cg';
	/**
	 * @var array Components that end with one of these strings will not have SASS files.
	 */
	private static $tails = ['Controller', 'Service', 'Factory', 'Filter', 'Provider', 'Config'];

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
			$this->updateComponent($dir, $package, $parts);

			return;
		}
		$this->updatePackage($dir, $package, $parts);
	}

	/**
	 * @param string $package
	 *
	 * @return bool
	 */
	private static function isComponent($package)
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
	 * @param string $file
	 *
	 * @return bool
	 */
	private static function requireAll($dir, $file)
	{
		return self::requireIgnore($dir, $file)
			? false
			: is_dir($dir.DIRECTORY_SEPARATOR.$file) && !BuildTask::startsWith($file, self::PREFIX);
	}

	/**
	 * @param string $dir
	 * @param string $file
	 *
	 * @return bool
	 */
	private static function requireFile($dir, $file)
	{
		$isJs = BuildTask::endsWith($file, ".js");
		$isComponent = is_dir($dir.DIRECTORY_SEPARATOR.$file) && BuildTask::startsWith($file, "cg");

		return self::requireIgnore($dir, $file)
			? false
			: ($isJs || $isComponent);
	}

	/**
	 * @param string $dir
	 * @param string $file
	 *
	 * @return bool
	 */
	private static function requireIgnore($dir, $file)
	{
		$test = BuildTask::endsWith($file, ".Test.js") || BuildTask::startsWith($file, '.');

		return $file[0] == "_" || $file == "." || $file == ".." || $test;
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 *
	 * @return array
	 */
	private function templateAll_js($dir, $package, array $parts)
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

		$files = scandir($dir);
		asort($files);
		$count = count($lines);
		foreach ($files as $file)
		{
			if (self::requireAll($dir, $file))
			{
				$lines[] = sprintf('goog.require("%s.%s.All");', $name, str_replace(".js", "", $file));
			}
		}
		if ($count != count($lines))
		{
			$lines[] = '';
		}
		foreach ($files as $file)
		{
			if (self::requireFile($dir, $file))
			{
				$lines[] = sprintf('goog.require("%s.%s");', $name, str_replace(".js", "", $file));
			}
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
	private function templateAll_sass($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;
		$lines = [
			"/** Do not edit. This file was auto-created **/"
		];

		$files = scandir($dir);
		asort($files);
		$count = count($lines);
		foreach ($files as $file)
		{
			if (!is_dir($dir.$DS.$file) || BuildTask::startsWith($file, ".") || BuildTask::startsWith($file,
																									  self::PREFIX)
			)
			{
				continue;
			}
			if (!file_exists($dir.$DS.$file.$DS."_All.scss"))
			{
				continue;
			}
			$lines[] = sprintf('@import "%s/All";', $file);
		}
		if ($count != count($lines))
		{
			$lines[] = '';
		}
		foreach ($files as $file)
		{
			if (!is_dir($dir.$DS.$file) || BuildTask::startsWith($file, ".") || !BuildTask::startsWith($file,
																									   self::PREFIX)
			)
			{
				continue;
			}
			if (!file_exists($dir.$DS.$file.$DS.$file.".scss"))
			{
				continue;
			}

			$lines[] = sprintf('@import "%s/%s";', $file, $file);
		}

		return $lines;
	}

	/**
	 * @param string $package
	 * @param array  $parts
	 *
	 * @return array
	 */
	private function templatePackage_js($package, array $parts)
	{
		$namespace = implode(".", $parts);

		return array(
			sprintf('goog.provide("%s.%s");', $namespace, $package),
			sprintf('goog.require("%s");', $namespace)
		);
	}

	/**
	 * @param string $package
	 * @param array  $parts
	 *
	 * @returns string
	 */
	private function templateSASS($package, array $parts)
	{
		$import = implode("/", array_fill(0, count($parts) - 1, ".."));

		return array(
			"@import \"{$import}/Config\";",
			"",
			".{$package} {",
			"}"
		);
	}

	/**
	 * @param string $package
	 * @param array  $parts
	 *
	 * @return array
	 */
	private function templateTest($package, array $parts)
	{
		$namespace = implode(".", $parts);

		return array(
			"describe('{$namespace}.{$package}',function()",
			"{",
			"\tbeforeEach(module('cgTag'));",
			"});"
		);
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	private function updateComponent($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;

		// create the test file
		$test = $dir.$DS.$package.".Test.js";
		$this->writeFile($test, $this->templateTest($package, $parts));

		if (!self::isComponent($package))
		{
			return;
		}

		// create the SASS file
		$sass = $dir.$DS.$package.".scss";
		$this->writeFile($sass, $this->templateSASS($package, $parts));
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	private function updatePackage($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;
		$jsAll = $dir.$DS."_All.js";
		$sassAll = $dir.$DS."_All.scss";
		$jsPackage = $dir.$DS."_Package.js";

		// write _ALL.js
		$this->writeFile($jsAll, $this->templateAll_js($dir, $package, $parts), true);

		// write _All.sass
		$this->writeFile($sassAll, $this->templateAll_sass($dir, $package, $parts), true);

		// write _Package.js
		$this->writeFile($jsPackage, $this->templatePackage_js($package, $parts));
	}

	/**
	 * @param string $file
	 * @param array  $lines
	 * @param bool   $overwrite
	 */
	private function writeFile($file, array $lines, $overwrite = false)
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