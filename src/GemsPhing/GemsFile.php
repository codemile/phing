<?php

namespace GemsPhing;

/**
 * Class GemsFile
 *
 * @package GemsPhing
 */
class GemsFile
{
	/**
	 * Asserts that a file exists
	 *
	 * @param string $file
	 *
	 * @throws \BuildException
	 */
	public static function exists($file)
	{
		$path = self::toPath($file);
		GemsAssert::isTrue($path, "File not found: {$path}");
	}

	/**
	 * @param string $file
	 */
	public static function isReadable($file)
	{
		$path = self::toPath($file);
		GemsAssert::isTrue(is_readable($path), "Can not read: {$path}");
	}

	/**
	 * @param string $file
	 */
	public static function isWritable($file)
	{
		$path = self::toPath($file);
		GemsAssert::isTrue(is_writable($path), "Can not write: {$path}");
	}

	/**
	 * Asserts that a path to a file is valid.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @throws \BuildException
	 */
	private static function toPath($file)
	{
		GemsAssert::noneEmptyString($file, "Expecting a path to a file.");

		$real_file = realpath($file);
		GemsAssert::notEmpty($real_file, "Invalid file path: {$file}");

		return $real_file;
	}

	/**
	 * Writes data to a file.
	 *
	 * @param string $file
	 * @param mixed  $data
	 *
	 * @throws \BuildException
	 */
	public static function write($file, $data)
	{
		GemsAssert::noneEmptyString($file, "Expecting a path to a file.");

		$path = self::toPath($file);
		GemsFile::isWritable($path);

		$num = file_put_contents($path, $data);
		GemsAssert::isNotFalse($num, "Failed to write file: $path");
	}

	/**
	 * Reads the contents of a text file.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @throws \BuildException
	 */
	public static function read($file)
	{
		GemsAssert::noneEmptyString($file, "Expecting a path to a file.");

		$path = self::toPath($file);
		GemsFile::exists($path);
		GemsFile::isReadable($path);

		$str = file_get_contents($path);
		GemsAssert::isTrue(is_string($str), "Unexpected error reading file.");

		return $str;
	}

	/**
	 * Converts the contents of a text file into an array of strings.
	 *
	 * @param string $text
	 *
	 * @return array
	 * @throws \BuildException
	 */
	public static function toArray($text)
	{
		GemsAssert::isTrue(is_string($text), "Expected a string.");

		// convert to an array
		$lines = explode(PHP_EOL, trim($text));
		GemsAssert::notEmpty($lines, "File is empty.");

		return $lines;
	}
}