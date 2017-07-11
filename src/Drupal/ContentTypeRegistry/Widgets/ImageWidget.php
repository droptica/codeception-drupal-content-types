<?php
/**
 * @file
 * Represents a standard image upload widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal\ContentTypeRegistry\Widgets;

/**
 * Class ImageWidget
 *
 * @package Codeception\Module\Drupal\ContentTypeRegistry\Widgets
 */
class ImageWidget extends Widget
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'Image';
    }

    /**
     * {@inheritdoc}
     */
    public function fill($I, $value = null)
    {
        $I->attachFile($this->getCssOrXpath(), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCssOrXpath($option = '')
    {
        if ($this->hasSelector()) {
            return $this->getSelector();
        } else {
            return '#' . $this->getSelector() . '-0-upload';
        }
    }
}
