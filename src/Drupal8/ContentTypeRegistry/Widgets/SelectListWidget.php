<?php

/**
 * @file
 * Represents a select list widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class SelectListWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class SelectListWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Select list';
  }

  /**
   * {@inheritdoc}
   */
  public function fill($I, $value = NULL) {
    if (!is_null($value)) {
      $selector = $this->getSelector('#', '');
      $this->scrollToElement($I, $selector);
      $I->selectOption($selector, $value);
    }
  }
}
