<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * Creates empty unit tests for new JS files.
 */
class JasmineTask extends BuildTask
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

				// only folders that start with "cg"
				if (!$this->startsWith($package, "cg"))
				{
					continue;
				}

				$file = $dir.$DS.$package.".Test.js";

				if (file_exists($file))
				{
					continue;
				}

				$this->log($file);

				$namespace = implode(".", $parts);

				$lines = array(
					"describe('{$namespace}.{$package}', function()",
					"{",
					"\tbeforeEach(module('cgTag'));",
					"\tpending();",
					"});"
				);

				file_put_contents($file, implode("\n", $lines));
			}
		}
	}

	/**
	 * @param string $path The path to the root.
	 */
	public function setRoot($path)
	{
		$this->root = realpath($path);
	}
}