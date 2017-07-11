<?php
/**
 * @file
 * Represents a full dynamic address widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal\ContentTypeRegistry\Widgets;

/**
 * Class BookWidget
 *
 * @package Codeception\Module\Drupal\ContentTypeRegistry\Widgets
 */
class BookWidget extends Widget
{
    /**
     * Elements that make up the address. Keys are the individual field labels. Values are the end part of the selector.
     *
     * @var array
     */
    protected $elements = array();

    /**
     * Constructor.
     */
    public function __construct($yaml = array())
    {
        $this->name = 'Book widget';
    }

    public function getCssOrXpath($option = '')
    {
        return "#edit-book-bid";
    }

    /**
     * {@inheritdoc}
     *
     * The $value should be an array where the keys are:
     * - book: the book title
     * - book_parent: the title of the book parent page
     */
    public function fill($I, $value = null)
    {
        if (!empty($value)) {

            if ($value['book']) {
                $I->selectOption("#edit-book-bid", $value['book']);
                $I->waitForElementVisible("#edit-book-plid");
                $I->selectOption("#edit-book-plid", $value['book_parent']);
            } else {
                $I->selectOption("#edit-book-bid", '<create a new book>');
                $I->waitForText("This will be the top-level page in this book.");
            }
        }
    }
}
