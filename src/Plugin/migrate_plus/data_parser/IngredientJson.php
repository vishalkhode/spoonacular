<?php

namespace Drupal\spoonacular\Plugin\migrate_plus\data_parser;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\spoonacular\Utility\ArrayHelper;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "ingredient_json",
 *   title = @Translation("Ingredient JSON")
 * )
 */
class IngredientJson extends Json implements ContainerFactoryPluginInterface {

  protected function getSourceData($url) {
    $response = parent::getSourceData($url);
    $source_data = $this->createSouceData($response);
    return ArrayHelper::filterDuplicates($source_data, 'unique_name');
  }

  protected function createSouceData($source_data) {
    foreach($source_data['results'] as $data) {
      if (isset($data['recipe']['extendedIngredients'])) {
        foreach($data['recipe']['extendedIngredients'] as $ingredient) {
          $ingredient['unique_name'] = $ingredient['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          $returnData[] = $ingredient;
        }
      }
    }
    return $returnData ?? [];
  }

}
