<?php

use GemsPhing\GemsTask;

/**
 * MySQL
 *
 * Sends a SQL file to MySQL for execution on a database.
 */
class MySQLTask extends GemsTask
{
	/**
	 * Property for Database name
	 */
	protected $database;
	/**
	 * Property for file
	 */
	protected $file;
	/**
	 * Property for password
	 */
	protected $password;
	/**
	 * Property for username
	 */
	protected $user;

	/**
	 * @throws BuildException
	 */
	function main()
	{
		// check supplied attributes
		$this->assertProperty("file", "file");
		$this->assertProperty("database", "string");
		$this->assertProperty("user", "string");
		$this->assertProperty("password", "string");

		$this->shell("mysql --user={$this->user} --password={$this->password} --database={$this->database} < {$this->file}");
	}

	/**
	 * Set Property for Database
	 *
	 * @param string $value
	 */
	public function setDatabase($value)
	{
		$this->database = $value;
	}

	/**
	 * Sets the SQL file.
	 *
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * Set Property for Password
	 *
	 * @param string $value
	 */
	public function setPassword($value)
	{
		$this->password = $value;
	}

	/**
	 * Set Property for User
	 *
	 * @param string $value
	 */
	public function setUser($value)
	{
		$this->user = $value;
	}
}