<?php

/**
 * @file
 * Represents an autocomplete widget on a Drupal entity form.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

/**
 * Class AutocompleteWidget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
class AutocompleteWidget extends Widget
{
  /**
   * Constructor.
   */
  public function __construct()
  {
    $this->name = 'Autocomplete Widget';
  }

  /**
   * {@inheritdoc}
   *
   * @todo: filling autocomplete fields.
   */
  public function getCssOrXpath() {
    switch ($this->getField()->getWidgetMachineName()) {
      case 'entity_reference_autocomplete':
        // @todo: Handle multiple input.
        return $this->getSelector('#', '-0-target-id');

      case 'entity_reference_autocomplete_tags':
        return $this->getSelector('#', '-target-id');

      default:
        return $this->getSelector('#', '');
    }
  }
}
