<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * Handles executing Google's Closure Dependency generator for JavaScript.
 */
class DepsTask extends BuildTask
{
	/**
	 * @var FileSet[] A collection of FileSet objects.
	 */
	private $filesets = array();
	/**
	 * @var bool|string The name of the output file.
	 */
	private $output = false;
	/**
	 * @var bool|string The location of the web root folder.
	 */
	private $webroot = false;

	/**
	 * @return FileSet The new file set object.
	 */
	public function createFileSet()
	{
		$num = array_push($this->filesets, new FileSet());

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
		if ($this->output === false)
		{
			throw new BuildException("You must specify an output file.", $this->location);
		}

		$DS = DIRECTORY_SEPARATOR;
		$closure_library = "..{$DS}..{$DS}closure-library";

		$files = array(
			"--root=$closure_library{$DS}closure{$DS}goog"
		);

		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			$dir = realpath($scanner->getBasedir());
			if (!file_exists($dir))
			{
				throw new BuildException("Directory doesn't exist: $dir");
			}
			$folder = basename($dir);
			$files[] = "--root_with_prefix=\"".$dir." ../js/$folder\"";
		}

		// add files to parameters.
		$this->params[] = implode(" ", $files);
		$this->params[] = "--output_file={$this->webroot}{$this->output}";

		$options = implode(" ", $this->params);

		$this->shell("python {$closure_library}{$DS}closure{$DS}bin{$DS}build{$DS}depswriter.py $options");
	}

	/**
	 * @param string $filename Name of the output file.
	 */
	public function setOutput($filename)
	{
		$this->output = $filename;
	}

	/**
	 * @param string $path The path to the web root.
	 */
	public function setWebroot($path)
	{
		$this->webroot = realpath($path);
	}
}