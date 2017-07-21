<?php

/**
 * @file
 * Represents a link widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class LinkWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class LinkWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Link Widget';
  }

  /**
   * {@inheritdoc}
   *
   * For this field, $value should be an array with the 'title' and 'url' keys.
   */
  public function fill($I, $value = NULL) {
    if (!empty($value)) {
      if (isset($value['title'])) {
        $this->scrollToElement($I, $this->getSelector('#', '-0-title'));
        $I->fillField($this->getSelector('#', '-0-title'), $value['title']);
      }
      if (isset($value['url'])) {
        $this->scrollToElement($I, $this->getSelector('#', '-0-uri'));
        $I->fillField($this->getSelector('#', '-0-uri'), $value['url']);
      }
    }
  }
}
