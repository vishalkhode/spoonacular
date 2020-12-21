<?php

namespace Drupal\spoonacular\Plugin\migrate_plus\data_parser;

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
class RecipeJson extends Json {

  /**
   * {@inheritdoc}
   */
  public function getSourceData($url) {
    $response = parent::getSourceData($url);
    $source_data = $this->createSouceData($response);
    return ArrayHelper::filterDuplicates($source_data, 'ingredient_name');
  }

  /**
   * Parse the input data and create source data for recipes migration.
   */
  protected function createSouceData($source_data) {
    foreach ($source_data['results'] as $data) {
      if (isset($data['recipe']['extendedIngredients'])) {
        foreach ($data['recipe']['extendedIngredients'] as $ingredient) {

          // @todo we can add this in function as it's used at multiple places.
          $ingredient_name = preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          // We need to add unique field which we can use for migration lookup.
          $ingredient['ingredient_name'] = $ingredient['id'] . '_' . $ingredient_name;
          $ingredient['para_id'] = $data['id'] . '_' . $ingredient_name;
          $returnData[] = $ingredient;
        }
      }
    }
    return $returnData ?? [];
  }

}
