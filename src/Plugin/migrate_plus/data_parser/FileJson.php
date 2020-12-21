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

  protected function getSourceData($url) {
    $response = parent::getSourceData($url);
    $ingredients = $this->createSouceData($response);
    foreach($ingredients as $ingredient) {
      $ingredientImages[] = [
        'name' => $ingredient['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']) . ':ingredient',
        'image' => 'https://spoonacular.com/cdn/ingredients_500x500/' . $ingredient['image'],
      ];
    }

    foreach($response['results'] as $result) {
       $recipe_images[] = [
        'name' => $result['id'] . ':recipe',
        'image' => $result['image'],
      ]; 
    }
    $allImagesCombine = array_merge($ingredientImages, $recipe_images);
    return ArrayHelper::filterDuplicates($allImagesCombine, 'name');
  }
  
  protected function createSouceData($source_data) {
    foreach($source_data['results'] as $data) {
      if (isset($data['recipe']['extendedIngredients'])) {
        foreach($data['recipe']['extendedIngredients'] as $ingredient) {
          $ingredient['unique_name'] = $data['id'] . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
          $returnData[] = $ingredient;
        }
      }
    }
    return $returnData ?? [];
  }

}