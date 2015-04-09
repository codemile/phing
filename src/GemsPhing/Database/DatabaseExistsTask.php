<?php

use GemsPhing\GemsTask;

/**
 * DatabaseExists
 *
 * Checks if the database contains any tables.
 */
class DatabaseExistsTask extends GemsTask
{
	/**
	 * Property for Database name
	 */
	protected $database;
	/**
	 * Property for password
	 */
	protected $password;
	/**
	 * A table prefix to filter by.
	 */
	protected $prefix = '';
	/**
	 * The property to output the result to.
	 */
	protected $property;
	/**
	 * Property for username
	 */
	protected $user;
	/**
	 * @var PDO The database connection.
	 */
	private $db;

	/**
	 * Main-Method for the Task
	 *
	 * @return  void
	 * @throws  BuildException
	 */
	public function main()
	{
		// check supplied attributes
		$this->assertProperty("database", "string");
		$this->assertProperty("user", "string");
		$this->assertProperty("password", "string");

		$this->log("Checking if database exists.");

		// connect to the database
		$this->db = new PDO("mysql:host=localhost;dbname={$this->database};charset=UTF-8", $this->user,
							$this->password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		// make sure the change log table exists
		if (empty($this->prefix))
		{
			$result = $this->db->query("SHOW TABLES");
		}
		else
		{
			$result = $this->db->query("SHOW TABLES LIKE '{$this->prefix}%'");
		}
		$databaseExists = $result->rowCount() > 0;
		$this->project->setProperty($this->property, $databaseExists);
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
	 * Set Property for Password
	 *
	 * @param string $value
	 */
	public function setPassword($value)
	{
		$this->password = $value;
	}

	/**
	 * The table prefix to filter by.
	 *
	 * @param string $value
	 */
	public function setPrefix($value)
	{
		$this->prefix = $value;
	}

	/**
	 * The output property to set.
	 */
	public function setProperty($value)
	{
		$this->property = $value;
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