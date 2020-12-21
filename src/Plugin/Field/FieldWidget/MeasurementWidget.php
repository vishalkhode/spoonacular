<?php

namespace Drupal\spoonacular\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Plugin implementation of the 'measurement' widget.
 *
 * @FieldWidget(
 *   id = "measurement_widget",
 *   label = @Translation("Measurement"),
 *   field_types = {"measurement"}
 * )
 */
class MeasurementWidget extends WidgetBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $widget = [
      '#theme_wrappers' => ['container', 'form_element'],
      '#attributes' => ['class' => ['measurement-field-widget-inline']],
      '#attached' => [
        'library' => 'spoonacular/measurement',
      ],
    ];

    $widget['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      "#empty_option" => $this->t('- Select -'),
      '#options' => ['metric' => $this->t('Metric'), 'us' => $this->t('US')],
      '#default_value' => $items[$delta]->type ?? '',
    ];

    $widget['quantity'] = [
      '#type' => 'number',
      '#min' => 1,
      '#title' => $this->t('Quantity'),
      '#default_value' => $items[$delta]->quantity ?? '',
    ];

    $widget['unit'] = [
      '#type' => 'textfield',
      '#size' => '10',
      '#title' => $this->t('Unit'),
      '#default_value' => $items[$delta]->unit ?? '',
    ];
    return $widget;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => $value) {
      foreach (['type', 'quantity', 'unit'] as $subfield) {
        if ($value[$subfield] === '') {
          $values[$delta][$subfield] = NULL;
        }
      }
    }
    return $values;
  }

}
