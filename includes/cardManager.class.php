<?php

/**
* Class to manage card dat\a
*
* @author truehybridx
* @version 1.0
* @since 05/12/2019
*/

class CardManager {
	protected $con = null;
    protected $pdoOptions = array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL);

	/**
	 * Default constructor
	 *
	 * @param   PDOConnection  $con  connection to the database
	 */
    public function __construct($con) {
		$this->con = $con;
    }



    public function test() {
        $rs = $this->pdo->prepare($sql, $this->pdoOptions);
		$rs->bindParam(1, $appid, PDO::PARAM_STR);

		try {
			$rs->execute();
		} catch (Exception $e) {
			error_log('functions.class.php: getAllRolesWithAppUsage; DB error occurred retrieving roles for appID: ' . $appid);
			return array();
		}
    }
}