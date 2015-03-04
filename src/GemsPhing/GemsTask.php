<?php

namespace GemsPhing;

/**
 * Class BuildTask
 */
class GemsTask extends \Task
{
	/**
	 * @var array A list of parameters for a shell command.
	 */
	protected $params = array();

	/**
	 * Checks if a string ends with a string.
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0)
		{
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}

	/**
	 * Checks if a string starts with a string.
	 *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public static function startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}

	/**
	 *  This is here. Must be overloaded by real tasks.
	 */
	public function main()
	{
		throw new \BuildException("Task not implemented.");
	}

	/**
	 * Checks if the property has been assign.
	 *
	 * @param string $name
	 * @param string $type
	 *
	 * @throws \BuildException
	 */
	protected function assertProperty($name, $type)
	{
		$value = $this->$name;
		if ($value === null)
		{
			throw new \BuildException("You must specify a {$name} property", $this->location);
		}

		switch ($type)
		{
			case 'int':
				if (!is_int($value))
				{
					throw new \BuildException("The {$name} property must be an integer.", $this->location);
				}
				break;
			case 'string':
				if (strlen($value) == 0)
				{
					throw new \BuildException("The {$name} property can not be an empty string.", $this->location);
				}
				break;
			case 'file':
				if (!file_exists($value))
				{
					throw new \BuildException(sprintf('File %s does not exist.', $value), $this->location);
				}
				$content = file_get_contents($value);
				if (strlen($content) == 0)
				{
					throw new \BuildException(sprintf('Supplied file %s is empty', $value), $this->location);
				}
				break;
			case 'dir':
				if (!is_dir($value))
				{
					throw new \BuildException(sprintf('%s does not a directory.', $value), $this->location);
				}
				break;
		}
	}

	/**
	 * @param \FileSet[] $fileset A collection of FileSet objects.
	 *
	 * @returns array of files.
	 *
	 * @throws \BuildException
	 */
	protected function getFiles($fileset)
	{
		$files = array();
		foreach ($fileset as $fs)
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
			throw new \BuildException("No files found for task.", $this->location);
		}

		return $files;
	}

	/**
	 * Executes a command in the OS.
	 *
	 * @param string $command
	 *
	 * @return string
	 * @throws \BuildException
	 */
	protected function shell($command)
	{
		$command = "$command 2>&1";
		$this->log("Executing: $command");
		exec($command, $output, $code);
		$this->log("RETURN: $code");
		foreach ($output as $line)
		{
			$this->log($line);
		}
		if ((int)$code !== 0)
		{
			throw new \BuildException("Execution of a shell command returned a non-zero result.", $this->location);
		}

		return $output;
	}

	/**
	 * Sets the param only if value is true.
	 *
	 * @param bool   $value Must be true
	 * @param string $str   The value to set
	 */
	protected function toggle($value, $str)
	{
		if (!empty($value) && $value)
		{
			$this->params[] = $str;
		}
	}
}