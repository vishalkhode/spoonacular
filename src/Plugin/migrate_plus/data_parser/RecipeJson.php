<?php

namespace Drupal\spoonacular\Plugin\migrate_plus\data_parser;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\spoonacular\Utility\ArrayHelper;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "recipe_json",
 *   title = @Translation("Recipe JSON")
 * )
 */
class RecipeJson extends Json implements ContainerFactoryPluginInterface {

  public function getSourceData($url) {
    $response = parent::getSourceData($url);
    $source_data = $this->createSouceData($response);
    return ArrayHelper::filterDuplicates($source_data, 'ingredient_name');
  }

  protected function createSouceData($source_data) {
    foreach($source_data['results'] as $data) {
      if (isset($data['recipe']['extendedIngredients'])) {
        foreach($data['recipe']['extendedIngredients'] as $ingredient) {
          $ingredient['ingredient_name'] = $ingredient['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          $ingredient['para_id'] = $data['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          $returnData[] = $ingredient;
        }
      }
    }
    return $returnData ?? [];
  }

}
