<?php

namespace Drupal\spoonacular\Plugin\migrate_plus\data_parser;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\spoonacular\Utility\ArrayHelper;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "cuisine_json",
 *   title = @Translation("Cuisine Parser JSON")
 * )
 */
class CuisineJson extends Json implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($url) {
    $response = parent::getSourceData($url);
    $cuisines_data = $this->createSourceData($response);
    return ArrayHelper::filterDuplicates($cuisines_data, 'name');
  }

  /**
   * Parse the input data and create source data for image file migration.
   */
  protected function createSourceData(array $source_data) {
    foreach ($source_data['results'] as $result) {
      if (isset($result['recipe']['cuisines'])) {
        foreach ($result['recipe']['cuisines'] as $cuisine) {
          $returnData[] = ['name' => $cuisine];
        }
      }
    }
    return $returnData ?? [];
  }

}
