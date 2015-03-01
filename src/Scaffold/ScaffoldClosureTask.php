<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * Handles the creation of the _All.js and _Package.js files.
 */
class ScaffoldClosureTask extends BuildTask
{

	/**
	 * @var FileSet[] A collection of FileSet objects.
	 */
	private $filesets = array();

	/**
	 * @var bool|string The location of the root folder.
	 */
	private $root = false;

	/**
	 * @return FileSet The new file set object.
	 */
	public function createFileSet()
	{
		$num = array_push($this->filesets, new FileSet());

		return $this->filesets[$num - 1];
	}

	/**
	 */
	public function main()
	{
		if ($this->root === false)
		{
			throw new BuildException("You must specify the root folder.", $this->location);
		}

		$DS = DIRECTORY_SEPARATOR;

		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			foreach ($scanner->getIncludedDirectories() as $included)
			{
				$dir = realpath($scanner->getBasedir()).$DS.$included;
				$parts = explode($DS, substr($dir, strlen($this->root) + 1));

				$package = array_pop($parts);
				$parent = implode(".", $parts);

				// ignore folders that start with "cg"
				if ($this->startsWith($package, "cg"))
				{
					continue;
				}

				$this->updateAll($dir, $dir.$DS."_All.js", $parent, $package);
				$this->createPackage($dir.$DS."_Package.js", $parent, $package);
			}
		}
	}

	/**
	 * @param $path string The path to the root.
	 */
	public function setRoot($path)
	{
		$this->root = realpath($path);
	}

	/**
	 * @param string $file
	 * @param string $parent
	 * @param string $package
	 */
	private function createPackage($file, $parent, $package)
	{
		if (file_exists($file))
		{
			return;
		}

		$lines = array(
			"/** Do not edit. This file was auto-created **/",
			sprintf('goog.provide("%s.%s");', $parent, $package),
			sprintf('goog.require("%s");', $parent)
		);

		file_put_contents($file, implode(PHP_EOL, $lines).PHP_EOL, FILE_APPEND);
	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @param string $parent
	 * @param string $package
	 */
	private function updateAll($dir, $file, $parent, $package)
	{
		$DS = DIRECTORY_SEPARATOR;

		$base = empty($package)
			? $parent
			: $parent.".".$package;

		$lines = array(
			"/** Do not edit. This file was auto-created **/",
			sprintf('goog.provide("%s.All");', $base),
			sprintf('goog.require("%s");', $base),
			''
		);

		$files = scandir($dir);
		asort($files);
		foreach ($files as $js)
		{
			if ($js[0] == "_" || $js == "." || $js == ".." || $this->endsWith($js, ".Test.js"))
			{
				continue;
			}

			if ($this->endsWith($js, ".js") || (is_dir($dir.$DS.$js) && $this->startsWith($js, "cg")))
			{
				array_push($lines, sprintf('goog.require("%s.%s");', $base, str_replace(".js", "", $js)));
				continue;
			}

			if (is_dir($dir.$DS.$js))
			{
				array_push($lines, sprintf('goog.require("%s.%s.All");', $base, str_replace(".js", "", $js)));
			}
		}

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