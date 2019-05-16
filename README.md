# ygDM
Random application written in PHP to manage my collection of YuGiOh card.

## Motivation
Started getting back into the card game and wanted a way to inventory the cards and maybe later on run reports on them.

## Features
* Uses the card ID number to pull card information from a web api (not provided in config, google yugioh card api)
* Saves the card image locally
* Saves the card data to a database (currently expecting SQLSRV but uses PDO so should be simple to adapt)
* (BETA) Attempts to use Tesseract JS to try to read images with the card ID but cannot get images with enough focus.