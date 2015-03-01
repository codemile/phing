<?php

/**
 * Fixes URLs in a source map so that they will load in the browser.
 */
class SourceMap
{
	public static function process($mapFile, $targetURL, Task $task)
	{
		$mapFile = realpath($mapFile);
		if (!file_exists($mapFile))
		{
			throw new BuildException("SourceMap.php: File not found: $mapFile");
		}

		$relative = pathinfo($mapFile, PATHINFO_DIRNAME);
		$task->log($relative);

		// fix mapping paths
		$map = json_decode(file_get_contents($mapFile));
		$map->file = str_replace("\\", "/", $targetURL);
		$arr = array();
		$prefix = "/weblocal/";
		foreach ($map->sources as $source)
		{
			$path = self::fixPath($relative, $source);
			if ($path === false)
			{
				throw new BuildException("SourceMap.php: Unable to resolve path: $source");
			}

			$matches = 0;
			$plugin = false;

			if (preg_match('/\/Plugin\/([\w\d]+)\/weblocal\//', $path, $matches) === 1)
			{
				$plugin = strtolower($matches[1]);
			}

			$str = str_replace("\\", "/", $path);
			$str = preg_replace("/^.*\/weblocal/", "", $str);
			if ($plugin !== false)
			{
				$str = "/".$plugin.$str;
			}
			$task->log($str);
			$arr[] = $str;
		}
		$map->sources = $arr;

		$map = json_encode($map);
		$map = str_replace("\\/", "/", $map);

		file_put_contents($mapFile, $map);

		return $arr;
	}

	private static function fixPath($relative, $path)
	{
		$tmp = str_replace("/", DIRECTORY_SEPARATOR, $path);
		$tmp = realpath($tmp);
		if ($tmp !== false)
		{
			return $tmp;
		}
		$tmp = str_replace("/", DIRECTORY_SEPARATOR, $relative.DIRECTORY_SEPARATOR.$path);
		$tmp = realpath($tmp);
		if ($tmp !== false)
		{
			return $tmp;
		}
		throw new BuildException("SourceMap.php: Unable to resolve: $path");
	}
}