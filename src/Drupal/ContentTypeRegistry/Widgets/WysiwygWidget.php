<?php
/**
 * @file
 * Represents a widget on a Drupal entity form that has a wysiwyg editor.
 */

namespace Codeception\Module\Drupal\ContentTypeRegistry\Widgets;

/**
 * Class WysiwygWidget
 *
 * @todo placeholder class until we figure out how to deal with this type of field!
 *
 * @package Codeception\Module\Drupal\ContentTypeRegistry\Widgets
 */
class WysiwygWidget extends Widget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'Text area with a summary';
    }

    /**
     * {@inheritdoc}
     */
    public function getCssOrXpath($option = '')
    {
        return '#' . $this->getSelector() . '-0';
    }

    /**
     * {@inheritdoc}
     */
    public function fill($I, $value = null)
    {
        if (!empty($value)) {
            // Change the format to plain text in order to get around the way that we can't fill the CKEditor itself.
            $I->selectOption($this->getCssOrXpath() . '-format--2', 'plain_text');

            $I->fillField($this->getCssOrXpath() . '-value', $value);
        }
    }
}
