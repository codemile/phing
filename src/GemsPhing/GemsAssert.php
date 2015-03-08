<?php

namespace GemsPhing;

/**
 * Assertions make testing and coding easier. The code can be written linearly with fewer branches making for less code
 * coverage during tests.
 *
 * @package GemsPhing
 */
class GemsAssert
{
	/**
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isTrue($value, $msg)
	{
		if ($value !== true)
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isFalse($value, $msg)
	{
		if ($value !== false)
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isNotTrue($value, $msg)
	{
		if ($value === true)
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isNotFalse($value, $msg)
	{
		if ($value === false)
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * Asserts that a value is not empty.
	 *
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function notEmpty($value, $msg)
	{
		if (empty($value))
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * Asserts that a value is a string and not empty.
	 *
	 * @param mixed $value
	 * @param string $msg
	 */
	public static function noneEmptyString($value, $msg)
	{
		self::isTrue(is_string($value),$msg);
		self::notEmpty($value,$msg);
	}

	/**
	 * Asserts that a value is empty.
	 *
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isEmpty($value, $msg)
	{
		if (!empty($value))
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * Asserts that two values are equal.
	 *
	 * @param mixed  $value
	 * @param mixed  $expected
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function areEqual($value, $expected, $msg)
	{
		if ($value != $expected)
		{
			throw new \BuildException($msg);
		}
	}

	/**
	 * Asserts that a value is truthy.
	 *
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isTruthy($value, $msg)
	{
		if (!!$value === true)
		{
			return;
		}
		throw new \BuildException($msg);
	}

	/**
	 * Asserts that a value is falsey
	 *
	 * @param mixed  $value
	 * @param string $msg
	 *
	 * @throws \BuildException
	 */
	public static function isFalsey($value, $msg)
	{
		if (!$value === true)
		{
			return;
		}
		throw new \BuildException($msg);
	}
}