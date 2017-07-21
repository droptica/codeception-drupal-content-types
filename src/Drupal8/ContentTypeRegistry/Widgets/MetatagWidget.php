<?php

/**
 * @file
 * Represents a metatag field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class MetatagWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class MetatagWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Metatag Widget';
  }
}
