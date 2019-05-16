<?php

/**
* Class to manage card dat\a
*
* @author truehybridx
* @version 1.0
* @since 05/12/2019
*/

require_once('app_config.php');
require_once('card.class.php');

class CardManager {
	protected $con = null;
	protected $api = null;
  protected $pdoOptions = array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL);

	/**
	 * Default constructor
	 *
	 * @param   PDOConnection  $con  connection to the database
	 */
	public function __construct($con) {

		global $config;

		$this->api = $config['apiPath'];
		$this->con = $con;
	}

	/**
	 * Pulls the requested card from the DB or pulls it from the web
	 *
	 * @param   string  $id  	ID of the card to pull
	 * @return  Card       		The retrieved card
	 */
	public function retrieveCard($id) {

		if (empty($id) || !is_numeric($id)) {
			throw new Exception('A valid numeric ID is required.');
		}

		//Check for in db
		$sql = "SELECT id, name, type, [desc], attack, defense, level,
							race, attribute, image, quantity
						FROM dbo.CardData
						WHERE id = ?";
		$rs = $this->con->prepare($sql, $this->pdoOptions);
		$rs->bindParam(1, $id, PDO::PARAM_STR);

		try {
			$rs->execute();
		} catch (Exception $e) {
			$err = 'cardManager.class.php: retrieveCard; DB error occurred retrieving card: ' . $id;
			error_log($err);
			throw new Exception($err);
		}

		$result = $rs->fetch(PDO::FETCH_ASSOC);

		$rs = null;

		//If not in DB pull from web
		$card = null;
		if (!empty($result)) {
			$card = new Card();
			$card->id = $result['id'];
			$card->name = $result['name'];
			$card->type = $result['type'];
			$card->desc = $result['desc'];
			$card->attack = $result['attack'];
			$card->defense = $result['defense'];
			$card->level = $result['level'];
			$card->race = $result['race'];
			$card->attribute = $result['attribute'];
			$card->image = $result['image'];
			$card->quantity = $result['quantity'];
			$card->new = false;
		} else {
			$card = $this->retrieveCardFromWeb($id);
			if (!empty($card)) {
				$card->image = $this->saveCardImage($card->image);
				$this->saveNewCard($card);
			} else {
				throw new Exception('Card using the id: ' . $id . ' could not be found.');
			}
		}

		//Return the card
		return $card;
	}

	/**
	 * Pulls the card from the web
	 *
	 * @param   string  $id  	ID of the card to pull
	 * @return  Card       		The retrieved card
	 */
	private function retrieveCardFromWeb($id) {

		if (empty($id) || !is_numeric($id)) {
			throw new Exception('A valid numeric ID is required.');
		}

		$ch = curl_init();
					
		curl_setopt_array($ch, array(
			CURLOPT_URL => $this->api . '?name=' . $id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));
		
		$response  = curl_exec($ch);
		$err = curl_error ($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$ch = null;

		if ($httpcode !== 200) {
			throw new Exception('An error occurred retrieving the card: ' . $id);
		}

		$json = json_decode($response, true);

		if (empty($json)) {
			throw new Exception('An error occurred parsing response: ' . $response);
		}

		if (!count($json[0])) {
			throw new Exception('The request card was not found on the web: ' . $id);
		}

		$cardData = $json[0][0];

		$card = new Card();
		$card->id = (!empty($cardData['id'])) ? $cardData['id'] : '';
		$card->name = (!empty($cardData['name'])) ? $cardData['name'] : '';
		$card->type = (!empty($cardData['type'])) ? $cardData['type'] : '';
		$card->desc = (!empty($cardData['desc'])) ? $cardData['desc'] : '';
		$card->attack = (!empty($cardData['atk'])) ? $cardData['atk'] : '';
		$card->defense = (!empty($cardData['def'])) ? $cardData['def'] : '';
		$card->level = (!empty($cardData['level'])) ? $cardData['level'] : '';
		$card->race = (!empty($cardData['race'])) ? $cardData['race'] : '';
		$card->attribute = (!empty($cardData['attribute'])) ? $cardData['attribute'] : '';
		$card->image = (!empty($cardData['image_url'])) ? $cardData['image_url'] : '';
		$card->quantity = 1;
		$card->new = true;

		return $card;
	}

	/**
	 * Saves the card image locally
	 *
	 * @param   string  $imageUrl  	URL to the image
	 * @return  string             	Path to the card image
	 */
	private function saveCardImage($imageUrl) {
		if (empty($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) { 
			throw new Exception('A valid URL is required to save image.');
		}

		$arr = explode('/', $imageUrl);

		if (empty($arr)) {
			throw new Exception('Cannot parse image name from URL: ' . $imageUrl);
		}

		$filename = $arr[count($arr)-1];

		$ch = curl_init();
					
		curl_setopt_array($ch, array(
			CURLOPT_URL => $imageUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));
		
		$image  = curl_exec($ch);
		$err = curl_error ($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$ch = null;

		if ($httpcode !== 200) {
			throw new Exception('An error occurred retrieving the card: ' . $id);
		}

		if (empty($image)) {
			throw new Exception('An error occurred retrieving card image data from web.');
		}

		$imageFile = './cardImages/' . $filename;
		$result = file_put_contents($imageFile, $image);

		if (empty($result)) {
			throw new Exception('An error occurred saving the card image locally.');
		}

		return $imageFile;
	}

	/**
	 * Saves the card to the database
	 *
	 * @param   Card  $card  The card to save
	 * @return  bool         True on success
	 */
	private function saveNewCard($card) {
		if (empty($card)) {
			throw new Exception('Unable to save an empty card.');
		}

		$sql = "INSERT INTO dbo.CardData(id, name, type, [desc], attack, defense, level,
							race, attribute, image, quantity)
						VALUES(?,?,?,?,?,?,?,?,?,?,?)";
		$rs = $this->con->prepare($sql, $this->pdoOptions);
		$rs->bindParam(1, $card->id, PDO::PARAM_STR);
		$rs->bindParam(2, $card->name, PDO::PARAM_STR);
		$rs->bindParam(3, $card->type, PDO::PARAM_STR);
		$rs->bindParam(4, $card->desc, PDO::PARAM_STR);
		$rs->bindParam(5, $card->attack, PDO::PARAM_STR);
		$rs->bindParam(6, $card->defense, PDO::PARAM_STR);
		$rs->bindParam(7, $card->level, PDO::PARAM_STR);
		$rs->bindParam(8, $card->race, PDO::PARAM_STR);
		$rs->bindParam(9, $card->attribute, PDO::PARAM_STR);
		$rs->bindParam(10, $card->image, PDO::PARAM_STR);
		$rs->bindParam(11, $card->quantity, PDO::PARAM_INT);

		try {
			$rs->execute();
		} catch (Exception $e) {
			error_log('cardManager.class.php: saveNewCard; DB error occurred saving new card: ' . $card->name);
			error_log($e);
			var_dump($e);
			throw new Exception('An error occurred saving the card: ' . $card->name);
		}

		$rs = null;

		return true;
	}

	/**
	 * Sets the card quantity in the database
	 *
	 * @param   string  $id        	ID of the card
	 * @param   int  $quantity  		The quantity to set
	 * @return  bool             		True on success, false otherwise
	 */
	public function updateCardQuantity($id, $quantity) {
		if (empty($id) || !is_numeric($id)) {
			throw new Exception('A valid numeric ID is required.');
		}

		if (empty($quantity) || !is_numeric($quantity)) {
			throw new Exception('A valid numeric quantity is required.');
		}

		$sql = "UPDATE dbo.CardData
						SET quantity = ?
						WHERE id = ?";
		$rs = $this->con->prepare($sql, $this->pdoOptions);
		$rs->bindParam(1, $quantity, PDO::PARAM_STR);
		$rs->bindParam(2, $id, PDO::PARAM_STR);

		try {
			$rs->execute();
		} catch (Exception $e) {
			error_log('cardManager.class.php: updateCardQuantity; DB error occurred updating card quantity for : ' . $id);
			error_log($e);
			throw new Exception('An error occurred updating card quantity for: ' . $id);
		}

		$rs = null;

		return true;
	}
}