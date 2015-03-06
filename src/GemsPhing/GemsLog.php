<?php

namespace GemsPhing;

class GemsLog
{
	/**
	 * @var \Project
	 */
	private static $_project;

	/**
	 * Sets the current project being run.
	 *
	 * @param \Project $project
	 */
	public static function set(\Project $project)
	{
		self::$_project = $project;
	}

	/**
	 * @param string $msg
	 */
	public static function log($msg)
	{
		if(!self::$_project)
		{
			echo "$msg\n";
			return;
		}
		self::$_project->log($msg);
	}
}