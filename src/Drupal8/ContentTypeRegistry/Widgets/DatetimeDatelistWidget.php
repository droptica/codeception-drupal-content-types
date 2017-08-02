<?php

/**
 * @file
 * Represents a datetime datelist widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class DatetimeDatelistWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class DatetimeDatelistWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Datetime Datelist Widget';
  }

  /**
   * {@inheritdoc}
   *
   * For this field, $value should be an array with the 'title' and 'url' keys.
   */
  public function fill($I, $value = NULL) {
    if (!empty($value)) {
      $this->scrollToElement($I, $this->getSelector('#', '-0-value-year'));
      if (isset($value['year'])) {
        $I->selectOption($this->getSelector('#', '-0-value-year'), $value['year']);
      }
      if (isset($value['month'])) {
        $I->selectOption($this->getSelector('#', '-0-value-month'), $value['month']);
      }
      if (isset($value['day'])) {
        $I->selectOption($this->getSelector('#', '-0-value-day'), $value['day']);
      }
      if (isset($value['hour'])) {
        $I->selectOption($this->getSelector('#', '-0-value-hour'), $value['hour']);
      }
      if (isset($value['minute'])) {
        $I->selectOption($this->getSelector('#', '-0-value-minute'), $value['minute']);
      }
    }
  }
}
