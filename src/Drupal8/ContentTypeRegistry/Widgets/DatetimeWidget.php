<?php

/**
 * @file
 * Represents a datetime widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class DatetimeWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class DatetimeWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Datetime Widget';
  }

  /**
   * {@inheritdoc}
   *
   * For this field, $value should be an array with the 'title' and 'url' keys.
   */
  public function fill($I, $value = NULL) {
    if (!empty($value)) {
      $this->scrollToElement($I, $this->getSelector('#', '-0-value-date'));
      if (method_exists($I, 'executeJS')) {
        if (isset($value['date'])) {
          $I->executeJS("document.querySelector('{$this->getSelector('#', '-0-value-date')}').value = '{$value['date']}'");
        }
        if (isset($value['time'])) {
          $I->executeJS("document.querySelector('{$this->getSelector('#', '-0-value-time')}').value = '{$value['time']}'");
        }
      }
      else {
        if (isset($value['date'])) {
          $I->fillField($this->getSelector('#', '-0-value-date'), $value['date']);
        }
        if (isset($value['time'])) {
          $I->fillField($this->getSelector('#', '-0-value-time'), $value['time']);
        }
      }
    }
  }
}
