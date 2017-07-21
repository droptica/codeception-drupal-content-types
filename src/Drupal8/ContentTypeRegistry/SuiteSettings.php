<?php

/**
 * @file
 * Static class used to persist settings for individual suites.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry;

/**
 * Class SuiteSettings
 * 
 * @package Codeception\Module\Drupal8\ContentTypeRegistry
 */
class SuiteSettings {
  /**
   * The name of the suite, which should correspond to the name of the folder/dir that the suite is in.
   *
   * @var string
   */
  public static $suiteName;
}
