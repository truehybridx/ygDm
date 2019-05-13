<?php

/**
* Class to represent card data
*
* @author truehybridx
* @version 1.0
* @since 05/12/2019
*/

class Card {

    /**
     * Global ID of the card
     *
     * @var string
     */
    public $id;

    /**
     * Name of the card
     *
     * @var string
     */
    public $name;

    /**
     * Type of the card (Monster, Trap, Spell, etc)
     *
     * @var string
     */
    public $type;

    /**
     * Description on the card
     *
     * @var string
     */
    public $desc;

    /**
     * Attack points (if a monster)
     *
     * @var string
     */
    public $attack;

    /**
     * Defense points (if a monster)
     *
     * @var string
     */
    public $defense;

    /**
     * Power level (if a monster)
     *
     * @var string
     */
    public $level;

    /**
     * The race (if a monster)
     *
     * @var string
     */
    public $race;

    /**
     * Elemental attribute
     *
     * @var string
     */
    public $attribute;

    /**
     * Location to the image of the card
     *
     * @var string
     */
    public $image;

    /**
     * Current quantity in inventory
     *
     * @var int
     */
    public $quantity;

}