<?php

/**
 * @file
 * Yaml implementation of ContentTypeRegistryStorageInterface.
 */

namespace Codeception\Module\Drupal8\ContentTypeRegistry;

use Symfony\Component\Yaml\Yaml;
use Codeception\Exception\ConfigurationException as ConfigurationException;
use Codeception\Module\Drupal8\ContentTypeRegistry\Fields\Field;

use Drupal\node\Entity\NodeType;
use Drupal\field\FieldConfigInterface;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;

/**
 * Retrieve a list of content types for this site from yaml configuration.
 *
 * @package Codeception\Module\Drupal8\ContentTypeRegistry
 */
class ContentTypeRegistryYamlStorage implements ContentTypeRegistryStorageInterface
{
  /**
   * An array of ContentType objects.
   *
   * @var ContentType[]
   */
  protected static $contentTypes = array();

  /**
   * An array of field definitions that apply to multiple content types.
   *
   * This is for use when the field is exactly the same on multiple types, to avoid defining it a load of times for
   * no reason.
   *
   * @var Field[]
   */
  protected static $globalFields = array();

  /**
   * An array of extra definitions that apply to multiple content types.
   *
   * This is for use when the extra is exactly the same on multiple types, to avoid defining it a load of times for
   * no reason.
   *
   * @var Field[]
   */
  protected static $globalExtras = array();

  /**
   * The parsed Yaml configuration, stored to avoid having to process it multiple times from loading a file.
   *
   * @var array
   */
  protected $config = array();

  /**
   * The parsed Yaml configuration, stored to avoid having to process it multiple times from loading a file.
   *
   * @var array
   */
  protected $moduleConfig = array();

  /**
   * The parsed Yaml custom fields configuration.
   *
   * @var array
   */
  protected $customFieldsConfig = array();

  /**
   * Here we initialize the internal static storage from the yaml.
   *
   * ContentTypeRegistryYamlStorage constructor.
   * @param $moduleConfig
   */
  public function __construct($moduleConfig)
  {
    $this->moduleConfig = $moduleConfig;

    $contentTypesAutoDump = $this->moduleConfig['contentTypesAutoDump'];

    if (function_exists('node_type_get_types') && $contentTypesAutoDump) {
      $this->dumpContentTypes();
    }

    if (empty(static::$globalFields)) {
      static::$globalFields = $this->loadGlobalFields();
    }
    if (empty(static::$globalExtras)) {
      static::$globalExtras = $this->loadGlobalExtras();
    }
    if (empty(static::$contentTypes)) {
      static::$contentTypes = $this->loadContentTypes();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isInitialised()
  {
    return !empty($this->config);
  }

  /**
   * {@inheritdoc}
   */
  public function parseDataSource()
  {
    $suite = SuiteSettings::$suiteName;
    $testsRoot = $this->moduleConfig['tests_root'];
    $contentTypesFile = $this->moduleConfig['contentTypesFile'];
    // Get content types from configuration.
    //
    // If there is a content types yaml file in the current suite, use it. Otherwise, look for a global content
    // types yaml file instead.

    $suiteConfigFile = $testsRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $suite .
      DIRECTORY_SEPARATOR . $contentTypesFile;
    $globalConfigFile = $testsRoot . DIRECTORY_SEPARATOR . 'tests/' . $contentTypesFile;

    if (file_exists($suiteConfigFile)) {
      $yaml = file_get_contents($suiteConfigFile);
      $this->config = Yaml::parse($yaml);
    } elseif (file_exists($globalConfigFile)) {
      $yaml = file_get_contents($globalConfigFile);
      $this->config = Yaml::parse($yaml);
    } else {
      throw new ConfigurationException("Content Type Registry: no configuration files found.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function parseCustomFields()
  {
    $suite = SuiteSettings::$suiteName;
    $testsRoot = $this->moduleConfig['tests_root'];
    $customFieldsFile = $this->moduleConfig['customFieldsFile'];

    // Get custom fields from configuration.
    $suiteFieldsConfigFile = $testsRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $suite .
      DIRECTORY_SEPARATOR . $customFieldsFile;
    $globalFieldsConfigFile = $testsRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $customFieldsFile;

    if (file_exists($suiteFieldsConfigFile)) {
      $yaml = file_get_contents($suiteFieldsConfigFile);
      $this->customFieldsConfig = Yaml::parse($yaml);
    } elseif (file_exists($globalFieldsConfigFile)) {
      $yaml = file_get_contents($globalFieldsConfigFile);
      $this->customFieldsConfig = Yaml::parse($yaml);
    }
  }

  function contentTypeFields($contentType) {
    $entityManager = \Drupal::service('entity_field.manager');
    $fields = [];

    if(!empty($contentType)) {
      $fields = array_filter(
        $entityManager->getFieldDefinitions('node', $contentType), function ($field_definition) {
          return $field_definition instanceof FieldConfigInterface;
        }
      );
    }

    return $fields;
  }

  /**
   * Dump drupal content types in module input format to .yml file.
   */
  public function dumpContentTypes() {
    $this->parseCustomFields();
    $testsRoot = $this->moduleConfig['tests_root'];
    $contentTypesAutoDumpFile = $this->moduleConfig['contentTypesAutoDumpFile'];
    $contentTypesSubmitSelector = $this->moduleConfig['contentTypesSubmitSelector'];
    $contentTypesConfig = array('ContentTypes' => array());
    $contentTypes = NodeType::loadMultiple();
//    $fieldTypesDefinitions = \Drupal::service('plugin.manager.field.field_type')->getDefinitions();

    /** @var NodeType $contentType */
    foreach ($contentTypes as $contentType) {
      $ctName = $contentType->id();
      $fields = $this->contentTypeFields($ctName);

      $contentTypesConfig['ContentTypes'][$ctName]['humanName'] = $contentType->label();
      $contentTypesConfig['ContentTypes'][$ctName]['machineName'] = $ctName;
      $contentTypesConfig['ContentTypes'][$ctName]['fields'] = array();
      $contentTypesConfig['ContentTypes'][$ctName]['submit'] = $contentTypesSubmitSelector;
      if (isset($this->customFieldsConfig['Global']['title'])) {
        $contentTypesConfig['ContentTypes'][$ctName]['fields']['title'] = $this->customFieldsConfig['Global']['title'];
      }

      /** @var EntityFormDisplayInterface $entity_form_display */
      $entity_form_display = \Drupal::entityTypeManager()
        ->getStorage('entity_form_display')
        ->load('node.' . $ctName . '.default');

      /** @var FieldConfigInterface $field */
      foreach ($fields as $field) {
        $fieldType = $field->getType();
        $fieldName = $field->getName();
        $fieldCardinality = $field->getFieldStorageDefinition()->getCardinality();
        $field_display_component = $entity_form_display->getComponent($fieldName);
        $fieldConfig = array(
          'machineName' => $fieldName,
          'label' => $field->getLabel(),
          'type' => $fieldType,
          'widget' => $field_display_component['type'],
          'required' => $field->get('required'),
          'cardinality' => $fieldCardinality,
        );
        $additionalFieldConfig = array();
        if (!empty($this->customFieldsConfig)) {
          if (isset($this->customFieldsConfig['Global']) && in_array($fieldName, array_keys($this->customFieldsConfig['Global']))) {
            if (isset($this->customFieldsConfig['Global'][$fieldName]['additional_config'])) {
              $additionalFieldConfig = $this->customFieldsConfig['Global'][$fieldName]['additional_config'];
              unset($this->customFieldsConfig['Global'][$fieldName]['additional_config']);
            }
            $fieldConfig = array_merge($fieldConfig, $this->customFieldsConfig['Global'][$fieldName]);
          }
          if (isset($this->customFieldsConfig['ContentTypes'][$ctName]) && in_array($fieldName, array_keys($this->customFieldsConfig['ContentTypes'][$ctName]))) {
            if (isset($this->customFieldsConfig['ContentTypes'][$ctName][$fieldName]['additional_config'])) {
              // override additional field config from global section
              $additionalFieldConfig = $this->customFieldsConfig['ContentTypes'][$ctName][$fieldName]['additional_config'];
              unset($this->customFieldsConfig['ContentTypes'][$ctName][$fieldName]['additional_config']);
            }
            $fieldConfig = array_merge($fieldConfig, $this->customFieldsConfig['ContentTypes'][$ctName][$fieldName]);
          }
        }
        $contentTypesConfig['ContentTypes'][$ctName]['fields'][$fieldName] = $fieldConfig;
        if (!empty($additionalFieldConfig)) {
          $additionalFieldConfigAction = isset($additionalFieldConfig['action']) ? $additionalFieldConfig['action'] : 'add';
          $additionalFields = isset($additionalFieldConfig['fields']) ? $additionalFieldConfig['fields'] : array();
          if ($additionalFieldConfigAction == 'replace') {
            unset($contentTypesConfig['ContentTypes'][$ctName]['fields'][$fieldName]);
          }
          foreach($additionalFields as $additionalFieldName => $additionalFieldSettings) {
            $contentTypesConfig['ContentTypes'][$ctName]['fields'][$additionalFieldName] = $additionalFieldSettings;
          }
        }
      }
    }

    $contentTypesConfigYaml = Yaml::dump($contentTypesConfig, 10, 2);
    $dumpFile = $testsRoot . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $contentTypesAutoDumpFile;
    file_put_contents($dumpFile, $contentTypesConfigYaml);
  }

  /**
   * {@inheritdoc}
   */
  public function loadGlobalFields()
  {
    // Make sure to initialise by reading the data source.
    if (!$this->isInitialised()) {
      $this->parseDataSource();
    }

    $globalFields = array();

    if (empty($this->config)) {
      throw new ConfigurationException("Content Type Registry: configuration file is invalid.");
    }

    if (isset($this->config['GlobalFields'])) {
      foreach ($this->config['GlobalFields'] as $fieldData) {
        $field = Field::parseYaml($fieldData);
        $globalFields[$field->getMachine()] = $field;
      }
    }

    return $globalFields;
  }

  /**
   * {@inheritdoc}
   */
  public function loadGlobalExtras()
  {
    // Make sure to initialise by reading the data source.
    if (!$this->isInitialised()) {
      $this->parseDataSource();
    }

    $globalExtras = array();

    if (empty($this->config)) {
      throw new ConfigurationException("Configuration file is invalid");
    }

    if (isset($this->config['GlobalExtras'])) {
      foreach ($this->config['GlobalExtras'] as $extraData) {
        $extra = Field::parseYaml($extraData);
        $globalExtras[$extra->getMachine()] = $extra;
      }
    }

    return $globalExtras;
  }

  /**
   * {@inheritdoc}
   *
   * @throws ConfigurationException
   */
  public function loadContentTypes()
  {
    // Make sure to initialise by reading the data source.
    if (!$this->isInitialised()) {
      $this->parseDataSource();
    }

    $globalFields = $this->loadGlobalFields();
    $globalExtras = $this->loadGlobalExtras();
    $contentTypes = array();

    if (empty($this->config)) {
      throw new ConfigurationException("Configuration file is invalid");
    }

    if (isset($this->config['ContentTypes'])) {
      foreach ($this->config['ContentTypes'] as $contentTypeData) {
        $contentType = ContentType::parseYaml($contentTypeData, $globalFields, $globalExtras);
        $contentTypes[$contentType->getMachineName()] = $contentType;
      }
    } else {
      throw new ConfigurationException("No Drupal content types are configured");
    }

    return $contentTypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getContentType($type)
  {
    return isset(static::$contentTypes[$type]) ? static::$contentTypes[$type] : null;
  }

  /**
   * {@inheritdoc}
   */
  public function getContentTypes()
  {
    return static::$contentTypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getGlobalField($field)
  {
    return isset(static::$globalFields[$field]) ? static::$globalFields[$field] : null;
  }

  /**
   * {@inheritdoc}
   */
  public function getGlobalFields()
  {
    return static::$globalFields;
  }

  /**
   * {@inheritdoc}
   */
  public function getGlobalExtra($extra)
  {
    return isset(static::$globalExtras[$extra]) ? static::$globalExtras[$extra] : null;
  }

  /**
   * {@inheritdoc}
   */
  public function getGlobalExtras()
  {
    return static::$globalExtras;
  }
}
