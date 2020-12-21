<?php

namespace Drupal\Tests\spoonacular\Traits;

use Drupal\Core\Serialization\Yaml;

/**
 * Description of newPHPClass
 *
 * @author vishalkhode
 */
trait MigrationTrait {

    public function getModulePath() {
      $module_handler = \Drupal::service('module_handler');
      $module_path = $module_handler->getModule('spoonacular')->getPath();
      return $module_path;
    }

  public function getDataFile() {
    $json_path = DRUPAL_ROOT . '/' . $this->getModulePath() . '/tests/data/recipe.json';
    return $json_path;
  }

  public function getDataJson() {
    $data = @file_get_contents($this->getDataFile());
    if ($data !== NULL) {
      $data = json_decode($data, true);  
    }
    return $data;
  }

  public function updateMigrationConfig() {
    $file = $this->getDataFile();
    $module_path = $this->getModulePath();
    $module_path = DRUPAL_ROOT . '/'. $module_path . '/config/install/migrate_plus.migration.';
    foreach(['cuisine_term', 'ingredient_term', 'media_recipe_ingredient', 'paragraph_ingredient', 'recipe_node', 'file_recipe_ingredient'] as $migration) {
      $data = Yaml::decode(file_get_contents($module_path . $migration . '.yml'));  
      $data['source']['urls'] = $file;
      $config_id = 'migrate_plus.migration.' . $migration ;
      \Drupal::configFactory()->getEditable($config_id)->setData($data)->save(TRUE);
    }
  }

}
