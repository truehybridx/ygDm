<?php

/**
* Class to handle connection to SQL database
*
* @author truehybridx
* @version 1.0
* @since 05/12/2019
*/

require_once('app_config.php');

class SQLDB {
	private $pdoCon;

	// Application database connection
	private $serverName = '';

	// Phones connection
	private $appUser = "";
	private $appPass = '';
	private $appDB = '';

	/**
	 * Generates needed connection to EVS
	 */
	public  function __construct() {

		global $config;

		$this->serverName = $config['servername'];
		$this->appUser = $config['username'];
		$this->appPass = $config['password'];
		$this->appDB = $config['database'];

		ini_set('mssql.textsize', '2147483647');
		ini_set('mssql.textlimit', '2147483647');

		try  {
			$conn = new PDO("sqlsrv:Server="  . $this->serverName . ";Database=" . $this->appDB, $this->appUser, $this->appPass);

			$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdoCon = $conn;

		} catch(PDOException  $e)  {   
			die( print_r( $e->getMessage() ) );   
			throw new Exception("Unable to connect to the application database");
		} 
	}

	/**
	 * Generates and returns a PDO connection resource
	 *
	 * @return resource PDO database connection
	 */
	public function getPDO() {
		return $this->pdoCon;
	}

}