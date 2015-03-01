<?php

require_once __DIR__."/DirTreeTask.php";

/**
 * This task checks every JS file in a directory tree. For each JS file that starts with "cg" it will relocate that
 * file to a sub-folder of the same name.
 */
class MigrateTask extends DirTreeTask
{
	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	protected function directory($dir, $package, array $parts)
	{
		// ignore folders that start with "cg"
		if ($this->startsWith($package, "cg"))
		{
			return;
		}

		$this->log('Updating: '.$dir);

		$DS = DIRECTORY_SEPARATOR;

		$files = scandir($dir);
		asort($files);
		foreach ($files as $file)
		{
			if ($file[0] == "_" || $file == "." || $file == ".." || $this->endsWith($file, ".Test.js"))
			{
				continue;
			}

			if (!$this->startsWith($file, "cg") || !$this->endsWith($file, ".js"))
			{
				continue;
			}

			$target = $dir.$DS.substr($file, 0, -3);
			if (is_dir($target))
			{
				continue;
			}

			$this->log('Moving: '.$file);

			mkdir($target);
			copy($dir.$DS.$file, $target.$DS.$file);
			unlink($dir.$DS.$file);
		}

		$this->log('');
	}
}