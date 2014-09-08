<?php
/**
 * @file
 * Represents a single on/off checkbox field on a Drupal entity.
 */

namespace Codeception\Module\Drupal\ContentTypeRegistry\Widgets;

use Codeception\Util\WebInterface;

/**
 * Class SingleCheckboxField
 *
 * @package Codeception\Module\Drupal\ContentTypeRegistry\Fields
 */
class SingleCheckboxWidget extends Widget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'Single on/off checkbox';
    }

    /**
     * {@inheritdoc}
     */
    public function fillField(WebInterface $I, $value = null)
    {
        if ($value == true) {
            $I->checkOption($this->getSelector());
        } else {
            $I->uncheckOption($this->getSelector());
        }
    }
}