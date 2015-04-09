<?php

use GemsPhing\GemsTask;

/**
 * DatabaseUpdater
 *
 * Handles the incremental updating of a database.
 */
class DatabaseUpdaterTask extends GemsTask
{
	/**
	 * Property for Database name
	 */
	protected $database;
	/**
	 * Property for Dir
	 */
	protected $dir;
	/**
	 * Property for Order
	 */
	protected $order;
	/**
	 * Property for password
	 */
	protected $password;
	/**
	 * Property for Table
	 */
	protected $table = 'changelogs';
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
	 * @throws  BuildException
	 */
	public function main()
	{
		// check supplied attributes
		$this->assertProperty("table", "string");
		$this->assertProperty("order", "file");
		$this->assertProperty("dir", "dir");
		$this->assertProperty("database", "string");
		$this->assertProperty("user", "string");
		$this->assertProperty("password", "string");

		$lines = file($this->order);

		$this->log("Performing database update.");

		// connect to the database
		$this->db = new PDO("mysql:host=localhost;dbname={$this->database};charset=UTF-8", $this->user,
							$this->password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		// make sure the change log table exists
		$tableExists = $this->db->query("SHOW TABLES LIKE '{$this->table}'")
								->rowCount() > 0;
		if (!$tableExists)
		{
			throw new BuildException("Database does not have a {$this->table} table.", $this->location);
		}

		// continue to do the update until
		// no updates are performed
		$repeat = true;
		while ($repeat)
		{
			$repeat = false;
			foreach ($lines as $line)
			{
				if (strlen(trim($line)) > 0 && !$this->startsWith($line, '#'))
				{
					if ($this->endsWith(strtolower(trim($line)), '.sql') && strstr($line, '=') !== false)
					{
						list($from_version, $filename) = explode('=', $line);
						if ($this->update(trim($from_version), trim($filename)))
						{
							$repeat = true;
						}
					}
					else
					{
						throw new BuildException("Unexpected line: $line", $this->location);
					}
				}
			}
		}
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
	 * Set Property for Dir containing the update SQL files.
	 *
	 * @param string $dir
	 */
	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * Set Property for Order containing the update order.
	 *
	 * @param string $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
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
	 * Set Property for Table
	 *
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
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

	/**
	 * Returns the latest version defined in the change log.
	 */
	private function currentVersion()
	{
		$rows = $this->db->query("SELECT version FROM {$this->table} ORDER BY created DESC LIMIT 1")
						 ->fetchAll(PDO::FETCH_ASSOC);
		if (empty($rows))
		{
			throw new BuildException("{$this->table} doesn't contain the current version. There must be at least one entry.",
									 $this->location);
		}

		return $rows[0]['version'];
	}

	/**
	 * Executes the update of the database.
	 *
	 * Returns True if the update was needed, otherwise
	 * False if not required.
	 *
	 * @param string $from_version
	 * @param string $filename
	 *
	 * @return bool
	 * @throws BuildException
	 */
	private function update($from_version, $filename)
	{
		if (!$this->startsWith($filename, 'update-'))
			throw new BuildException("Invalid filename for SQL update: $filename", $this->location);

		$path = $this->dir.'/'.$filename;
		if (!file_exists($path))
			throw new BuildException("SQL file is missing: $path", $this->location);

		$version = str_replace('.sql', '', str_replace('update-', '', strtolower($filename)));

		$current = $this->currentVersion();
		if ($from_version == $current)
		{
			$this->log("Migrating: {$from_version} to {$version}");
			$stmt = $this->db->prepare("INSERT INTO {$this->table}(version,previous,created,started) VALUE(:version,:previous,NOW(),NOW())");
			$stmt->bindValue(':version', $version, PDO::PARAM_STR);
			$stmt->bindValue(':previous', $from_version, PDO::PARAM_STR);
			$stmt->execute();
			$id = $this->db->lastInsertId();

			$output = $this->shell("mysql --database={$this->database} --password={$this->password} --user={$this->user} < {$path}");

			$stmt = $this->db->prepare("UPDATE {$this->table} SET log=:log,finished=NOW() WHERE id=:id");
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->bindValue(':log', implode("\n", $output), PDO::PARAM_STR);
			$stmt->execute();

			return true;
		}

		return false;
	}
}