<?php

namespace Drupal\spoonacular\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'measurement' field type.
 *
 * @FieldType(
 *   id = "measurement",
 *   label = @Translation("Measurement"),
 *   default_widget = "measurement_widget",
 *   default_formatter = "measurement_field_formatter",
 *   description = @Translation("Measurement.")
 * )
 */

class MeasurementField extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {

    $settings = [];
    $settings['storage']['type'] = [
      'type' => 'string',
      'maxlength' => 30,
    ];
    $settings['storage']['quantity'] = [
      'type' => 'string',
      'maxlength' => 255,
    ];
    $settings['storage']['unit'] = [
      'type' => 'string',
      'maxlength' => 30,
    ];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    $columns['type'] = [
      'type' => 'varchar',
      'length' => '255',
      'not null' => FALSE,
      'description' => "Measurement type value",
    ];

    $columns['quantity'] = [
      'type' => 'int',
      'size' => 'normal',
      'not null' => FALSE,
      'description' => "Quantity",
    ];

    $columns['unit'] = [
      'type' => 'varchar',
      'length' => '255',
      'not null' => FALSE,
      'description' => "Unit",
    ];
    return ['columns' => $columns];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['type'] = DataDefinition::create('string')->setLabel('Measurement Type');
    $properties['quantity'] = DataDefinition::create('string')->setLabel('Quantity');
    $properties['unit'] = DataDefinition::create('string')->setLabel('Unit');
    return $properties;
  }

}
