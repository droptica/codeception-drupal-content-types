<?php

/**
 * @file
 * Represents a wysiwyg text field widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

use Codeception\Util\Debug;

/**
 * Class WysiwygWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class WysiwygWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Wysiwyg Widget';
  }

  /**
   * {@inheritdoc}
   */
  public function fill($I, $value = NULL) {
    $selector = $this->getCssOrXpath();
    $this->scrollToElement($I, $selector);
    if (method_exists($I, 'executeJS')) {
      $I->executeJS("document.querySelector('" . $selector . "').style.visibility = 'visible'");
      $I->executeJS("document.querySelector('" . $selector . "').style.display = 'block'");

      // TODO: Handle also other editors.
      $I->executeJS("
        if (CKEDITOR !== undefined && CKEDITOR.instances !== undefined) {
          for(name in CKEDITOR.instances)
          {
              CKEDITOR.instances[name].destroy(true);
          }
        }
      ");
    }
    if (!empty($value)) {
      $I->fillField($selector, $value);
    }
  }
}
