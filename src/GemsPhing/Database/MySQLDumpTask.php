<?php

require_once dirname(__DIR__)."/BuildTask.php";

/**
 * MySQL
 *
 * Exports a database to a SQL file.
 */
class MySQLDumpTask extends BuildTask
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

	function main()
	{
		// check supplied attributes
		$this->assertProperty("file", "string");
		$this->assertProperty("database", "string");
		$this->assertProperty("user", "string");
		$this->assertProperty("password", "string");

		$this->shell("mysqldump --hex-blob --user={$this->user} --password={$this->password} {$this->database} > {$this->file}");
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