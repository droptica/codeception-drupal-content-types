<?php

/**
 * @file
 * Represents a standard image upload widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class FileUploadWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class FileUploadWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'FileUpload Widget';
  }

  /**
   * {@inheritdoc}
   */
  public function fill($I, $value = NULL) {
    if (isset($value['file'])) {
      $selector = $this->getSelector('input#', '-0-upload');
      $this->scrollToElement($I, $selector);
      $I->attachFile($selector, $value['file']);
      unset($value['file']);

      // PhpBrowser.
      if (!method_exists($I, 'waitForElement')) {
        $I->click($this->getSelector('#', '-0-upload-button'));
      }
      foreach ($value as $field_key => $field_value) {
        $selector = $this->getSelector('input[id*="', '-0-' . $field_key . '"]');
        // WebDriver.
        if (method_exists($I, 'waitForElement')) {
          $I->waitForElement($selector, 10);
        }
        $I->fillField($selector, $field_value);
      }
    }
  }
}
