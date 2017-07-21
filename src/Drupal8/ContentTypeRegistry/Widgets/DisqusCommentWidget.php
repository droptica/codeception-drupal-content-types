<?php

/**
 * @file
 * Represents a disqus comment field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class DisqusCommentWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class DisqusCommentWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Disqus Comment Widget';
  }
}
