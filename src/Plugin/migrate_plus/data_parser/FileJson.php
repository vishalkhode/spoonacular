<?php

namespace Drupal\spoonacular\Plugin\migrate_plus\data_parser;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\Json;
use Drupal\spoonacular\Utility\ArrayHelper;

/**
 * Obtain JSON data for migration.
 *
 * @DataParser(
 *   id = "file_json",
 *   title = @Translation("File Parser JSON")
 * )
 */
class FileJson extends Json implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  protected function getSourceData($url) {
    $response = parent::getSourceData($url);
    $ingredients = $this->createSouceData($response);

    foreach ($ingredients as $ingredient) {
      // @todo we can add this in function as it's used at multiple places.
      $ingredient_name = preg_replace('/[^a-z]/m', '_', $ingredient['name']);
      $ingredientImages[] = [
        'name' => $ingredient['id'] . '_' . $ingredient_name . ':ingredient',
        'image' => 'https://spoonacular.com/cdn/ingredients_500x500/' . $ingredient['image'],
      ];
    }

    foreach ($response['results'] as $result) {
      $recipe_images[] = [
        'name' => $result['id'] . ':recipe',
        'image' => $result['image'],
      ];
    }
    // @todo Many times json data is looped to extract the data as per our need.
    // Probably we can look for an alternate way to create source data.
    $allImagesCombine = array_merge($ingredientImages, $recipe_images);
    return ArrayHelper::filterDuplicates($allImagesCombine, 'name');
  }

  /**
   * Parse the input data and create source data for image file migration.
   */
  protected function createSouceData($source_data) {
    foreach ($source_data['results'] as $data) {
      if (isset($data['recipe']['extendedIngredients'])) {
        foreach ($data['recipe']['extendedIngredients'] as $ingredient) {

          // @todo we can add this in function as it's used at multiple places.
          // We need to add unique field which we can use for migration lookup.
          $ingredient['unique_name'] = $data['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          $returnData[] = $ingredient;
        }
      }
    }
    return $returnData ?? [];
  }

}
