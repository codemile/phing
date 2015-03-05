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
	public $params = array();

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
	 * @returns bool
	 * @throws \BuildException
	 */
	public function assertProperty($name, $type)
	{
		if (empty($name) || !property_exists($this, $name))
		{
			throw new \BuildException("{$name} property does not exist.", $this->location);
		}

		$value = $this->$name;
		if ($value === null)
		{
			throw new \BuildException("You must specify a {$name} property.", $this->location);
		}

		switch ($type)
		{
			case 'num':
			case 'number':
			case 'int':
			case 'integer':
			case 'float':
				if (!is_numeric($value))
				{
					throw new \BuildException("The {$name} property must be an integer.", $this->location);
				}
				break;
			case 'str':
			case 'string':
				if (!is_string($value) || empty($value))
				{
					throw new \BuildException("The {$name} property can not be an empty string.", $this->location);
				}
				break;
			case 'file':
				if (!file_exists($value))
				{
					throw new \BuildException(sprintf('File %s does not exist.', $value), $this->location);
				}
				$size = filesize($value);
				if ($size == 0 || $size === false)
				{
					throw new \BuildException(sprintf('Supplied file %s is empty.', $value), $this->location);
				}
				break;
			case 'directory':
			case 'folder':
			case 'dir':
				if (!is_dir($value))
				{
					throw new \BuildException(sprintf('%s is not a directory.', $value), $this->location);
				}
				break;
		}

		return true;
	}

	/**
	 * @param \FileSet[] $fileset A collection of FileSet objects.
	 * @param bool       $allowEmpty
	 *
	 * @return array of files.
	 *
	 * @throws \BuildException
	 */
	public function getFiles(array $fileset, $allowEmpty = true)
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

		if (!$allowEmpty && empty($files))
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
	public function shell($command)
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
	public function toggle($value, $str)
	{
		if (!empty($value) && $value)
		{
			$this->params[] = $str;
		}
	}
}