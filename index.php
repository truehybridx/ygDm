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

$con = new SQLDB();
$pdo = $con->getPDO();

$cardManager = new CardManager($pdo);
//46534755, 25880422, 20858318, 25655502

//If an ID is provided, go query the card
$cardDisplay = '';
if (!empty($_POST) && !empty($_POST['cardID'])) {
    
    if (!empty($_POST['mode']) && !empty($_POST['mode'])) {
        $quantity = $_POST['quantity'];
        if ($_POST['mode'] == 'inc') {
            $quantity++;
        } else if ($_POST['mode'] == 'dec') {
            $quantity--;
        }

        try {
            $cardManager->updateCardQuantity($_POST['cardID'], $quantity);
        } catch (Exception $e) {
            $cardDisplay = $e->getMessage();
        }
    }

    $card = null;
    try {
        $card = $cardManager->retrieveCard($_POST['cardID']);
        $cardDisplay = '
        <div class="starter-template">
            <h1>' . $card->name . '</h1>
            <p>' . $card->desc . '</p>
            <p>' . $card->attack . ' / ' . $card->defense . '</p>
            <image src="' . $card->image . '" />
            <form method="POST">
                <h2>Current Quantity: ' . $card->quantity . '</h2>
                <div class="form-group mx-sm-3 mb-2">
                    <input type="hidden" name="cardID" value="' . $card->id . '">
                    <input type="hidden" name="quantity" value="' . $card->quantity . '">
                </div>
                <button type="submit" name="mode" value="dec" class="btn btn-primary mb-2">Decrement</button>
                <button type="submit" name="mode" value="inc" class="btn btn-primary mb-2">Increment</button>
            </form>
        </div>';
    } catch (Exception $e) {
        $cardDisplay = $e->getMessage();
    }
    
}

?>


<!--

https://developers.google.com/web/fundamentals/media/capturing-images/

https://tutorialzine.com/2016/07/take-a-selfie-with-js

https://github.com/naptha/tesseract.js#tesseractjs



https://kdzwinel.github.io/JS-OCR-demo/



    -->

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">

    <title>Hello, world!</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
                    <div class="dropdown-menu" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </li> -->
            </ul>
            <!-- <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
            </form> -->
        </div>
    </nav>

    <main role="main" class="container">

        <div class="starter-template">
            <h1>Enter a card ID:</h1>
            <form method="POST">
                <div class="form-group mx-sm-3 mb-2">
                    <label for="cardID" class="sr-only">Enter Card ID:</label>
                    <input type="text" class="form-control" name="cardID" placeholder="">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Submit</button>
            </form>
        </div>

        <?php echo $cardDisplay ?>

    </main><!-- /.container -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>