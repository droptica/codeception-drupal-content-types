<?php

/**
 * @file
 * Represents a boolean checkbox widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class BooleanCheckboxWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class BooleanCheckboxWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Boolean Checkbox Widget';
  }

  /**
   * {@inheritdoc}
   */
  public function fill($I, $value = null) {
    $selector = $this->getSelector('#', '-value');
    $this->scrollToElement($I, $selector);
    if ($value == true) {
      $I->checkOption($selector);
    } else {
      $I->uncheckOption($selector);
    }
  }
}
