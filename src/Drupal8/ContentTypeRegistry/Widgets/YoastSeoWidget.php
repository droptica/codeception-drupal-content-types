<?php

/**
 * @file
 * Represents a YoastSeoWidget text field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;


/**
 * Class YoastSeoWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class YoastSeoWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Yoast Seo Widget';
  }

}
