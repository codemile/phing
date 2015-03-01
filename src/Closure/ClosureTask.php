<?php

require_once dirname(__DIR__)."/BuildTask.php";
require_once dirname(__DIR__)."/SourceMap.php";

/**
 * Handles executing Google's Closure Compiler for JavaScript.
 */
class ClosureTask extends BuildTask
{

	/**
	 * @var bool|string The entry point for the compiler.
	 */
	private $entry = false;
	/**
	 * @var FileSet[] A collection of FileSet objects.
	 */
	private $filesets = array();
	/**
	 * @var bool|string The type of optimization to perform.
	 */
	private $optimization = false;
	/**
	 * @var bool|string The name of the output file.
	 */
	private $output = false;
	/**
	 * @var bool True to generate source maps.
	 */
	private $sourceMap = false;
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
	 * Setup the task.
	 */
	public function init()
	{
		parent::init();
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
		if ($this->entry === false)
		{
			throw new BuildException("You must specify an entry point for the compiler.", $this->location);
		}

		$closure_library = "..\\..\\closure-library";

		$files = array(
			"--root=$closure_library\\closure\\goog",
			"--root=$closure_library\\third_party\\closure\\goog"
		);
		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			$dir = realpath($scanner->getBasedir());
			if (!file_exists($dir))
			{
				throw new BuildException("Directory doesn't exist: $dir");
			}
			$files[] = "--root=".$dir;
		}
		if (empty($files))
		{
			throw new BuildException("No directories found to compile.", $this->location);
		}

		// add files to parameters.
		$this->params[] = implode(" ", $files);

		/*        $this->params[] = $this->optimization === false
					? "--formatting PRETTY_PRINT"
					: $this->optimization;*/

		$compiler = array();

		if ($this->sourceMap)
		{
			$compiler[] = "--formatting=PRETTY_PRINT";
			$compiler[] = "--create_source_map={$this->webroot}{$this->output}.map";
			$compiler[] = "--output_wrapper='%output%//@ sourceMappingURL={$this->output}.map'";
		}

		$this->params[] = "--namespace {$this->entry}";
		$this->params[] = "--output_mode=compiled";
		//$this->params[] = "--output_mode=script";
		$this->params[] = "--compiler_jar=..\\gems\\builds\\compiler.jar";

		foreach ($compiler as $flag)
		{
			$this->params[] = "--compiler_flags=\"$flag\"";
		}

		$this->params[] = "--output_file={$this->webroot}{$this->output}";

		$options = implode(" ", $this->params);
		$this->shell("python $closure_library\\closure\\bin\\build\\closurebuilder.py $options");

		if ($this->sourceMap)
		{
			SourceMap::process($this->webroot.$this->output.".map", $this->output, $this);
		}
	}

	/**
	 * @param $value bool True to enable advanced optimizations.
	 */
	public function setAdvanced($value)
	{
		$this->optimization = false;
		if ($value === true)
		{
			$this->optimization = "--compilation_level ADVANCED_OPTIMIZATIONS";
		}
	}

	/**
	 * @param $value bool Generate $inject properties for AngularJS for functions annotated with @ngInject
	 */
	public function setAngularPass($value)
	{
		$this->toggle($value, "--angular_pass");
	}

	/**
	 * @param $str string Sets the char set to use for all files.
	 */
	public function setCharset($str)
	{
		$this->params[] = "--charset";
		$this->params[] = $str;
	}

	/**
	 * @param $value bool Allows usage of the const keyword.
	 */
	public function setConst($value)
	{
		$this->toggle($value, "--accept_const_keyword");
	}

	/**
	 * @param $value bool Enable debug information.
	 */
	public function setDebug($value)
	{
		$this->toggle($value, "--debug");
	}

	/**
	 * @param $str string Sets the entry point to the program. Must be good.provide symbols.
	 */
	public function setEntry($str)
	{
		$this->entry = $str;
	}

	/**
	 * @param $filename string Name of the output file.
	 */
	public function setOutput($filename)
	{
		$this->output = $filename;
	}

	/**
	 * @param $value bool True to enable simple optimizations.
	 */
	public function setSimple($value)
	{
		$this->optimization = false;
		if ($value === true)
		{
			$this->optimization = "--compilation_level SIMPLE_OPTIMIZATIONS";
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

	/**
	 * @param $path string The path to the web root.
	 */
	public function setWebroot($path)
	{
		$this->webroot = realpath($path);
	}

	/**
	 * @param $value bool True to enable whitespace only optimization.
	 */
	public function setWhiteSpaceOnly($value)
	{
		$this->optimization = false;
		if ($value === true)
		{
			$this->optimization = "--compilation_level WHITESPACE_ONLY";
		}
	}
}