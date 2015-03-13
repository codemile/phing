<?php

namespace GemsPhing\Scaffold;

use GemsPhing\GemsString;
use GemsPhing\GemsTask;

/**
 * Base task that handles the walking of a directory tree.
 */
abstract class DirTreeTask extends GemsTask
{
	/**
	 * @var \FileSet[] A collection of FileSet objects.
	 */
	private $filesets = array();

	/**
	 * @var string The location of the root folder.
	 */
	private $root;

	/**
	 * @return \FileSet The new file set object.
	 */
	public function createFileSet()
	{
		$num = array_push($this->filesets, new \FileSet());

		return $this->filesets[$num - 1];
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	abstract protected function directory($dir, $package, array $parts);

	/**
	 */
	public function main()
	{
		if (empty($this->root))
		{
			throw new \BuildException("You must specify the root folder, or the root folder does not exist.",
									  $this->location);
		}

		$DS = DIRECTORY_SEPARATOR;

		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			foreach ($scanner->getIncludedDirectories() as $included)
			{
				$dir = realpath(realpath($scanner->getBasedir()).$DS.$included);
				$parts = explode($DS, substr($dir, strlen($this->root) + 1));
				$package = array_pop($parts);

				if (GemsString::startsWith($dir, '.') || GemsString::startsWith($package, '.'))
				{
					continue;
				}

				$this->directory($dir, $package, $parts);
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
