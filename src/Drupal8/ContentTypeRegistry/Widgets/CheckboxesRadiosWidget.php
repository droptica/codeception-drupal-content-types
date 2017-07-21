<?php

/**
 * @file
 * Represents a checkboxes and radio buttons widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class CheckboxesRadiosWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class CheckboxesRadiosWidget extends Widget {
  /**
   * Constructor.
   */
  public function __construct() {
    $this->name = 'Check boxes/radio buttons Widget';
  }

  /**
   * {@inheritdoc}
   *
   * The $value parameter should contain an array of all options to be checked or unchecked.
   * The key should be the option key. The value should be true or false.
   */
  public function fill($I, $value = NULL) {
    // Skip this if there is no value set.
    if (!is_null($value)) {
      $field = $this->getField();
      // Radios.
      if ($field->getWidgetMachineName() == 'options_buttons' && ($field->getType() == 'boolean' || $field->getCardinality() == 1)) {
        $selector = $this->getSelector('#', "-{$value}");
        $this->scrollToElement($I, $selector);
        $I->selectOption($selector, $value);
      }
      // Checkboxes.
      else {
        foreach ($value as $option_key => $state) {
          $selector = $this->getSelector('#', "-{$option_key}");
          $this->scrollToElement($I, $selector);
          if ($state === TRUE) {
            $I->checkOption($selector);
          }
          else {
            $I->uncheckOption($selector);
          }
        }
      }
    }
  }
}
