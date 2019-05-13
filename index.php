<?php

/**
* Index page for the ygDeckMaster application
*
* @author truehybridx
* @version 1.0
* @since 05/12/2019
*/

require_once('includes/db.class.php');
require_once('includes/cardManager.class.php');

echo 'hello';

$con = new SQLDB();
$pdo = $con->getPDO();

$cardManager = new CardManager($pdo);
//46534755, 25880422, 20858318, 25655502

$card = $cardManager->retrieveCard('46534755');

echo '<pre>';
var_dump($card);
echo '</pre>';

$cardManager->saveCardImage($card->image);

//If an ID is provided, go query the card



//Display form for a new ID


//Display card info if ID was provided