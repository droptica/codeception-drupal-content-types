<?php

/**
 * @file
 * Represents a standard text field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class BasicWidget
 *
 * This is only wrapper for Widget class which already has all needed default methods.
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class BasicWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Basic Widget';
  }
}
