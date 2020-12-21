<?php

namespace Drupal\spoonacular\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementations for 'measurement' formatter.
 *
 * @FieldFormatter(
 *   id = "measurement_field_formatter",
 *   label = @Translation("Measurement Field Formatter"),
 *   field_types = {"measurement"}
 * )
 */
class MeasurementFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    $element['#attributes']['class'][] = 'double-field-unformatted-list';
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#settings' => $settings,
        '#field_settings' => $this->getFieldSettings(),
        '#item' => $item,
        '#theme' => 'double_field_item',
      ];
    }

    return $element;
  }

}
