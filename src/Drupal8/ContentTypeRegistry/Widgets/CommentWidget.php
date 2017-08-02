<?php

/**
 * @file
 * Represents a comment field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class CommentWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class CommentWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Comment Widget';
  }
}
