<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * Handles executing the Sass CSS compiler.
 */
class SassTask extends BuildTask
{

	/**
	 * @var bool|string The input file.
	 */
	private $inFile = false;

	/**
	 * @var bool|string The output file.
	 */
	private $outFile = false;

	/**
	 * @var bool True to generate source maps.
	 */
	private $sourceMap = false;

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
		if ($this->inFile === false || $this->outFile === false)
		{
			throw new BuildException("You must specify an input and output file.", $this->location);
		}

		$this->params[] = $this->sourceMap
			? "--sourcemap=auto"
			: "--sourcemap=none";

		$options = implode(" ", $this->params);

		$this->shell("sass $options {$this->inFile} {$this->outFile}");

		if (!$this->sourceMap)
		{
			return;
		}

		$this->log('Fixing Source Map');

		// fix the source map
		$outURL = realpath($this->outFile);
		$outURL = str_replace('\\', '/', $outURL);
		$outURL = preg_replace('/^.*\/weblocal\//', '/', $outURL);
		$this->log($outURL);

		SourceMap::process($this->outFile.".map", $outURL, $this);

		// fix the source map URL
		$output = file_get_contents($this->outFile);
		$output = preg_replace('/(\/\*#\s*sourceMappingURL=)(.*)(\.map\s*\*\/)/', "$1$outURL$3", $output);
		file_put_contents($this->outFile, $output);
	}

	/**
	 * @param bool $value True to just check syntax, don't evaluate.
	 */
	public function setCheck($value)
	{
		$this->toggle($value, "--check");
	}

	/**
	 * @param bool $value True to compact the output.
	 */
	public function setCompact($value)
	{
		$this->toggle($value, "--style compact");
	}

	/**
	 * @param bool $value True to compress the output.
	 */
	public function setCompress($value)
	{
		$this->toggle($value, "--style compressed");
	}

	/**
	 * @param string $encoding Specify the default encoding for Sass files.
	 */
	public function setEncoding($encoding)
	{
		$this->params[] = "-E";
		$this->params[] = $encoding;
	}

	/**
	 * @param bool $value True to expand the output.
	 */
	public function setExpand($value)
	{
		$this->toggle($value, "--style expanded");
	}

	/**
	 * @param bool $value True to recompile all Sass files, even if the CSS file is newer.
	 */
	public function setForce($value)
	{
		$this->toggle($value, "--force");
	}

	/**
	 * @param string $filename The input file.
	 */
	public function setInput($filename)
	{
		$this->inFile = $filename;
	}

	/**
	 * @param bool $value True to disable caching of Sass files.
	 */
	public function setNoCache($value)
	{
		$this->toggle($value, "--no-cache");
	}

	/**
	 * @param string $filename The output file.
	 */
	public function setOutput($filename)
	{
		$this->outFile = $filename;
	}

	/**
	 * @param string $path Adds a sass import path.
	 */
	public function setPath($path)
	{
		foreach (explode(',', $path) as $_path)
		{
			$this->params[] = "--load-path";
			$this->params[] = $_path;
		}
	}

	/**
	 * @param bool $value Enables source maps.
	 */
	public function setSourceMap($value)
	{
		$this->sourceMap = (!empty($value) && $value)
			? true
			: false;
	}
}