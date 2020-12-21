<?php

namespace Drupal\Tests\spoonacular\Traits;

use Drupal\Core\Serialization\Yaml;

/**
 * This trait is to used to add some common trait function needed by test.
 */
trait MigrationTrait {

  /**
   * Gets the spoonacular module path.
   *
   * @return string
   *   Returns the spoonacular module path.
   */
  public function getModulePath() {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('spoonacular')->getPath();
    return $module_path;
  }

  /**
   * Gets an absolute path to recipe JSON file.
   *
   * @return string
   *   Returns file path.
   */
  public function getDataFile() {
    $json_path = DRUPAL_ROOT . '/' . $this->getModulePath() . '/tests/data/recipe.json';
    return $json_path;
  }

  /**
   * Gets the file content for the recipe JSON file.
   *
   * @return string
   *   Returns file content.
   */
  public function getDataJson() {
    $data = @file_get_contents($this->getDataFile());
    if ($data !== NULL) {
      $data = json_decode($data, TRUE);
    }
    return $data;
  }

  /**
   * Updates all spoonacular migration configs to use correct JSON file path.
   */
  public function updateMigrationConfig() {
    $file = $this->getDataFile();
    $module_path = $this->getModulePath();
    $module_path = DRUPAL_ROOT . '/' . $module_path . '/config/install/migrate_plus.migration.';
    $migrateConfigNames = [
      'cuisine_term',
      'ingredient_term',
      'media_recipe_ingredient',
      'paragraph_ingredient',
      'recipe_node',
      'file_recipe_ingredient',
    ];
    foreach ($migrateConfigNames as $migration) {
      $data = Yaml::decode(file_get_contents($module_path . $migration . '.yml'));
      $data['source']['urls'] = $file;
      $config_id = 'migrate_plus.migration.' . $migration;
      \Drupal::configFactory()->getEditable($config_id)->setData($data)->save(TRUE);
    }
  }

}
