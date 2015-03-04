<?php

namespace GemsPhing;

/**
 * Some basic string methods.
 *
 * @package GemsPhing
 */
class GemsString
{
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
}