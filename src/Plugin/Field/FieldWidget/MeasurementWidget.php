<?php

namespace Drupal\spoonacular\Plugin\Field\FieldWidget;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\double_field\Plugin\Field\FieldType\DoubleField as DoubleFieldItem;
use Symfony\Component\Validator\ConstraintViolationInterface;

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

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    //dump($items);
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
      //'#required' => TRUE,
      '#options' => ['' => '- Select a value -', 'metric' => 'Metric', 'us' => 'US'],
      '#default_value' => $items[$delta]->type ?? '',
    ]; 
    $widget['quantity'] = [
      '#type' => 'number',
      '#min' => 1,
      //'#required' => TRUE,
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

  /**
   * {@inheritdoc}
   */
//  protected function getFieldSettings(): array {
//    $field_settings = parent::getFieldSettings();
//
//    foreach (['type', 'quantity', 'unit'] as $subfield) {
//      $subfield_type = $field_settings['storage'][$subfield]['type'];
//      if ($field_settings[$subfield]['list'] && !DoubleFieldItem::isListAllowed($subfield_type)) {
//        $field_settings[$subfield]['list'] = FALSE;
//      }
//    }
//
//    return $field_settings;
//  }

  /**
   * {@inheritdoc}
   */
//  public function getSettings(): array {
//    $settings = parent::getSettings();
//    $field_settings = $this->getFieldSettings();
//
//    foreach (['type', 'quantity', 'unit'] as $subfield) {
//      $widget_types = $this->getSubwidgets($field_settings['storage'][$subfield]['type'], $field_settings[$subfield]['list']);
//      // Use the type eligible widget type unless it is set explicitly.
//      if (!$settings[$subfield]['type']) {
//        $settings[$subfield]['type'] = key($widget_types);
//      }
//    }
//
//    return $settings;
//  }

  /**
   * Determines whether or not widget can render subfield label.
   */
//  private static function isLabelSupported(string $widget_type): bool {
//    return $widget_type != 'checkbox';
//  }

}
