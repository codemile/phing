<?php

use GemsPhing\GemsTask;

/**
 * @readme Tasks
 *
 * ###DepsTask
 *
 * Handles executing Google's Closure Dependency generator for JavaScript.
 *
 * Name | Type | Description | Default | Required
 * -----|------|-------------|---------|---------
 * output | string | The relative output path for the deps.js file. | n/a | Yes
 * library | string | The relative location to the closure library | n/a | Yes
 * prefix | string | A URL prefix to load JS files relative to goog/base.js | n/a | Yes
 *
 * ```xml
 * <taskdef name="deps" classname="GemsPhing.Closure.DepsTask"/>
 *
 * <deps output="www/deps.js" library="./www/closure-library" prefix="../../../src/cgTag">
 *        <fileset dir="./www/src/cgTag"/>
 * </deps>
 * ```
 */
class DepsTask extends GemsTask
{
	/**
	 * @var \FileSet[] A collection of FileSet objects.
	 */

	private $filesets = array();

	/**
	 * @var string The location of the google closure library.
	 * @see https://github.com/google/closure-library
	 */
	private $library;

	/**
	 * @var string The name of the output file.
	 */
	private $output;

	/**
	 * @var string The prefix for URLs in deps.js file.
	 */
	private $prefix;

	/**
	 * @return \FileSet The new file set object.
	 */
	public function createFileSet()
	{
		$num = array_push($this->filesets, new \FileSet());

		return $this->filesets[$num - 1];
	}

	/**
	 *  Called by the project to let the task do it's work. This method may be
	 *  called more than once, if the task is invoked more than once. For
	 *  example, if target1 and target2 both depend on target3, then running
	 *  <em>phing target1 target2</em> will run all tasks in target3 twice.
	 *
	 *  Should throw a BuildException if something goes wrong with the build
	 *
	 *  This is here. Must be overloaded by real tasks.
	 */
	public function main()
	{
		if (empty($this->output))
		{
			throw new BuildException("Must specify an output file.", $this->location);
		}

		if (empty($this->library))
		{
			throw new BuildException("Must specific location of Google closure library.", $this->location);
		}

		$DS = DIRECTORY_SEPARATOR;

		$params = [
			"--root={$this->library}{$DS}closure{$DS}goog"
		];

		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			$dir = realpath($scanner->getBasedir());
			if (!file_exists($dir))
			{
				throw new BuildException("Directory doesn't exist: $dir");
			}
			$params[] = "--root_with_prefix=\"{$dir} {$this->prefix}\"";
		}

		// add files to parameters.
		$this->params[] = implode(" ", $params);
		$this->params[] = "--output_file={$this->output}";

		$options = implode(" ", $this->params);

		$this->shell("python {$this->library}{$DS}closure{$DS}bin{$DS}build{$DS}depswriter.py $options");
	}

	/**
	 * @param string $library
	 */
	public function setLibrary($library)
	{
		$this->library = str_replace('/', DIRECTORY_SEPARATOR, $library);
	}

	/**
	 * @param string $filename Name of the output file.
	 */
	public function setOutput($filename)
	{
		$this->output = str_replace('/', DIRECTORY_SEPARATOR, $filename);
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}
}