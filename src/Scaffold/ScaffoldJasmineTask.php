<?php

require_once __DIR__."/ScaffoldComponentsTask.php";

/**
 * Creates empty unit test files in each component folder.
 */
class ScaffoldJasmineTask extends ScaffoldComponentsTask
{
	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updateComponentFolder($dir, $package, array $parts)
	{
		$DS = DIRECTORY_SEPARATOR;

		// create the test file
		$test = $dir.$DS.$package.".Test.js";
		$this->writeFile($test, $this->templateTest($package, $parts));
	}

	/**
	 * @param string $dir
	 * @param string $package
	 * @param array  $parts
	 */
	function updatePackageFolder($dir, $package, array $parts)
	{
		// do nothing
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
			"\tpending();",
			"});"
		);
	}
}