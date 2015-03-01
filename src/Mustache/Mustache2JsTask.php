<?php

die("Mustache2JsTask.php: This task is no longer used.");

require_once(dirname(__FILE__).'/../../Plugin/Gems/Vendor/Mustache/Autoloader.php');
Mustache_Autoloader::register();

/**
 * Compiles *.mustache files to a *.js files.
 */
class Mustache2JsTask extends BuildTask
{
	/**
	 * @var FileSet[] A collection of FileSet objects.
	 */
	private $filesets = array();
	/**
	 * @var bool|string Name of the JS file to generate.
	 */
	private $outputFile = false;
	/**
	 * @var bool|string Name of a Mustache file to use as a template.
	 */
	private $templateFile = false;

	/**
	 * @return FileSet The new file set object.
	 */
	public function createFileSet()
	{
		$num = array_push($this->filesets, new FileSet());

		return $this->filesets[$num - 1];
	}

	/**
	 * Compiles mustache files to javascript.
	 */
	public function main()
	{
		if ($this->templateFile === false)
		{
			throw new BuildException("Must specify a template file for creating JS.", $this->location);
		}
		if ($this->outputFile === false)
		{
			throw new BuildException("Must specify am output file for creating JS.", $this->location);
		}

		$mustache = new Mustache_Engine();
		$template = $mustache->loadTemplate(file_get_contents($this->templateFile));

		$files = array();
		foreach ($this->filesets as $fs)
		{
			$scanner = $fs->getDirectoryScanner($this->project);
			$base = $scanner->getBasedir();
			foreach ($scanner->getIncludedFiles() as $file)
			{
				$files[] = $base.'\\'.$file;
			}
		}
		if (empty($files))
		{
			throw new BuildException("No files found to compile.", $this->location);
		}

		$vars = array(
			"Templates" => array()
		);

		$base = pathinfo(realpath($this->templateFile), PATHINFO_DIRNAME);
		echo "BASE: $base\n";

		foreach ($files as $file)
		{
			if (realpath($file) == realpath($this->templateFile))
			{
				continue;
			}

			$str = file_get_contents($file);

			// fix: I'm having problems with DOM elements that attributes on multiple lines.
			$str = str_replace("\r", "", $str);
			$str = str_replace("\n", " ", $str);
			$str = preg_replace('/\s+/', ' ', $str);

			/*
						// compress html
						// Bug: this doesn't work for attributes on multiple lines.
						$re = '%(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:script|textarea|pre)\b))*+)(?:<(?>script|textarea|pre)\b|\z))%ix';
						$str = preg_replace($re,"",$str);
			*/

			// strip comments, but keep conditional comments
			$str = preg_replace('/<!--(?!<!)[^\[>].*?-->/', '', $str);

			// convert double quotes for attributes to single quotes
			$str = preg_replace_callback('/(\S+)=["]((?:.(?!["]?\s+(?:\S+)=|[>"]))+.)["]/', function ($match)
			{
				return sprintf("%s='%s'", $match[1], str_replace("'", "&#039;", $match[2]));
			}, $str);

			$str = json_encode($str);
			$str = substr($str, 1, strlen($str) - 2);

			$dir = pathinfo(realpath($file), PATHINFO_DIRNAME);
			$dir = str_replace("\\", ".", substr(str_replace($base, "", $dir), 1));

			$vars['Templates'][] = array(
				"directory" => $dir,
				"filename"  => pathinfo($file, PATHINFO_FILENAME),
				"mustache"  => $str
			);
		}

		$out = $template->render($vars);
		file_put_contents($this->outputFile, $out);
	}

	/**
	 * @param string $output The output file.
	 */
	public function setOutput($output)
	{
		$this->outputFile = $output;
	}

	/**
	 * @param string $filename The input file to use as a template for the output file.
	 */
	public function setTemplate($filename)
	{
		$this->templateFile = $filename;
	}
}