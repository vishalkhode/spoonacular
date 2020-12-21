<?php

namespace Drupal\spoonacular\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines a form that configures spoonacular settings.
 */
class SpoonacularConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spoonacular_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'spoonacular.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $config = $this->config('spoonacular.settings');
    $form['credentials'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Spoonacular API'),
      '#collapsible' => TRUE,
    ];

    $form['credentials']['use_mock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use Mock API'),
      '#default_value' => $config->get('use_mock'),
      '#description' => $this->t(
        'Select this, if you wish to use Mock API instead of Spoonacular API.'
      ),
    ];

    $form['credentials']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Spoonacular API Key'),
      '#default_value' => $config->get('api_key'),
      '#description' => $this->t(
        "<b>Important:</b> Add the API key to \$settings['spoonacular.apiKey'] in your site's settings.php file."
      ),
      '#disabled' => TRUE,
      '#states' => [
        'visible' => [
          ':input[id=edit-use-mock]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['credentials']['default_categories'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Categories'),
      '#default_value' => $config->get('default_categories'),
      '#description' => $this->t(
        "Add default Categories using comma seperated, to fetch data from. Ex: Asian, Italian, Mexican, Barbecue."
      ),
      '#states' => [
        'visible' => [
          ':input[id=edit-use-mock]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['credentials']['run_cron'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Migrate recipes during CRON run ?'),
      '#default_value' => $config->get('run_cron'),
      '#description' => $this->t(
        "Select this, if you wish to migrate content automatically when site cron runs."
      ),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('spoonacular.settings')
      ->set('api_key', $values['api_key'])
      ->set('use_mock', $values['use_mock'])
      ->set('default_categories', $values['default_categories'])
      ->set('run_cron', $values['run_cron'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
