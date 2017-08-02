<?php

/**
 * @file
 * Represents a widget used on web page forms to put data into or read data from a field.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry\Widgets;

use Codeception\Module\Drupal8\ContentTypeRegistry\Fields\Field;
use Codeception\Lib\Interfaces\Web;
use InvalidArgumentException;
use Codeception\Util\Debug;

/**
 * Class Widget
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry\Widgets
 */
abstract class Widget {
  /**
   * The name of the widget, as listed on the admin 'manage fields' page.
   *
   * @var string
   */
  protected $name;

  /**
   * A reference to the field object to which this widget is attached.
   *
   * @var Field
   */
  protected $field;

  /**
   * XPath or CSS selector to select this widget on the web page.
   *
   * @var string
   */
  protected $selector;

  /**
   * Provide a map between widget names and widget classes.
   *
   * Note that some fields don't have widgets listed on the 'manage fields' admin page (such as the "Node module
   * element" for the title field). These types are passed directly here so the type is used in the array rather than
   * the widget itself.
   *
   * @var array
   */
  protected static $widgetClasses = array(
    'string_textfield' => 'BasicWidget',
    'string_textarea' => 'BasicWidget',
    'text_textfield' => 'BasicWidget',
    'text_textarea' => 'WysiwygWidget',
    'text_textarea_with_summary' => 'WysiwygWidget',
    'datetime_default' => 'DatetimeWidget',
    'datetime_datelist' => 'DatetimeDatelistWidget',
    'datetime_timestamp' => 'DatetimeWidget',
    'number' => 'BasicWidget',
    'boolean_checkbox' => 'BooleanCheckboxWidget',
    'options_buttons' => 'CheckboxesRadiosWidget',
    'options_select' => 'SelectListWidget',
    'email_default' => 'BasicWidget',
    'file_generic' => 'FileUploadWidget',
    'image_image' => 'FileUploadWidget',
    'link_default' => 'LinkWidget',
    'entity_reference_autocomplete' => 'AutocompleteWidget',
    'entity_reference_autocomplete_tags' => 'AutocompleteWidget',
    'metatag_firehose' => 'MetatagWidget',
    'disqus_comment' => 'DisqusCommentWidget',
    'comment_default' => 'CommentWidget',

    // @todo: Other widgets.
  );

  /**
   * Gets the name of this widget.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets the name of this widget.
   *
   * Note that often this is not necessary as the widget's name is automatically set in the constructor.
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Gets the field to which this widget belongs.
   *
   * @return Field
   */
  public function getField() {
    return $this->field;
  }

  /**
   * Sets the field to which this widget belongs.
   *
   * @param Field $field
   */
  public function setField($field) {
    $this->field = $field;
  }

  /**
   * Gets the field selector.
   *
   * If a selector has been set explicitly, use it. Otherwise, derive one from the machine name of the parent field.
   * This is useful because the vast majority of selectors can be derived from the field's machine name, so this saves
   * the need to set it in every case.
   *
   * @param string $prefix
   *   Selector prefix.
   * @param string $suffix
   *   Selector suffix.
   *
   * @return string
   */
  public function getSelector($prefix, $suffix) {
    return isset($this->selector) ? $this->selector : $prefix . static::selectorFromMachine($this->getField()
        ->getMachine()) . $suffix;
  }

  /**
   * Gets a full CSS or XPath selector that can be applied to the web page to identify the widget.
   */
  public function getCssOrXpath() {
    return $this->getSelector('#', '-0-value');
  }

  /**
   * Sets the field selector.
   *
   * @param string $selector
   */
  public function setSelector($selector) {
    $this->selector = $selector;
  }

  /**
   * Create and return a widget of the specified type.
   *
   * @param string $yaml
   *   The yaml from contentTypes.yml that describes the field. Should contain the type of the widget, and,
   *   optionally, the widget name. The latter will be a string found in the 'widget' column on the 'manage fields'
   *   admin page, or, in the case of fields that don't have widgets listed there, the type of the field itself, from
   *   the 'type' column on that page.
   * @param Field $field
   *   The field that is to become the parent for this widget.
   *
   * @return Widget
   *   An object of a class that represents the widget that was specified.
   *
   * @throws InvalidArgumentException
   */
  public static function create($yaml, $field) {
    // Use the name of the widget. If there isn't one, use the type of the field instead.
    $type = isset($yaml['widget']) ? $yaml['widget'] : $yaml['type'];

    if (isset(static::$widgetClasses[$type])) {
      $class = 'Codeception\\Module\\Drupal8\\ContentTypeRegistry\\Widgets\\' .
        static::$widgetClasses[$type];

      /** @var Widget $widget */
      $widget = new $class($yaml);
      $widget->setField($field);

      return $widget;
    }
    else {
      throw new InvalidArgumentException(
        'Widget class could not be retrieved for the ' . $type . ' widget'
      );
    }
  }

  /**
   * Fill this widget on a web form.
   *
   * @param Web $I
   *   The WebInterface (like the actor) being used within the active test scenario.
   * @param mixed $value
   *   The value to put into the field's widget. Optional. If not provided, no fill will be attempted.
   */
  public function fill($I, $value = NULL) {
    if (!empty($value)) {
      $selector = $this->getCssOrXpath();
      $this->scrollToElement($I, $selector);
      $I->fillField($selector, $value);
    }
  }

  /**
   * Derive a selector from a machine name.
   *
   * For example, if the machine name is field_foo_bar, the derived selector would be edit-field-foo-bar-und.
   *
   * @param string $machine
   *   The machine name from which to derive a selector.
   *
   * @return string
   *   The selector that has been derived from the machine name.
   */
  public static function selectorFromMachine($machine) {
    $converted = str_replace("_", "-", $machine);
    return 'edit-' . $converted;
  }

  /**
   * Determine whether a selector has been set manually for this widget.
   *
   * @return bool
   *   True if the selector has been set manually. False otherwise.
   */
  public function hasSelector() {
    return isset($this->selector);
  }

  /**
   * When WebDriver is used, scroll view to given field.
   *
   * @param Web $I
   * @param string $element_selector
   * @param bool $xpath
   * @todo Move this function somewhere else?
   */
  public function scrollToElement($I, $element_selector, $xpath = FALSE) {
    if (method_exists($I, 'executeJS')) {
      // For WebDriver field needs to be in the view.
      try {
        // @todo: Scroll to view center.
        if ($xpath) {
          $I->executeJS("document.evaluate('{$element_selector}', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.scrollIntoView(false)");
        }
        else {
          $I->executeJS("document.querySelector('{$element_selector}').scrollIntoView(false)");
        }
      }
      catch (\Exception $e) {
        Debug::debug($e->getMessage());
      }
    }
  }
}
