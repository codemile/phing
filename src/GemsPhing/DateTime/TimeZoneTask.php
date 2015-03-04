<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * Class TimeZoneTask
 */
class TimeZoneTask extends BuildTask
{
	/**
	 * @var bool|string
	 */
	private $timezone = false;

	/**
	 *
	 */
	function init()
	{
		$this->timezone = 'America/Toronto';
	}

	/**
	 *
	 */
	function main()
	{
		date_default_timezone_set($this->timezone);
	}

	/**
	 * @param string $value
	 */
	function setZone($value)
	{
		$this->timezone = $value;
	}
}