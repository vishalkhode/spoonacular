<?php

namespace Drupal\Tests\spoonacular\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Entity\Migration;
use Drupal\Tests\spoonacular\Traits\MigrationTrait;

/**
 * Description of SpoonacularMigrateTest
 *
 * @author vishalkhode
 */
class SpoonacularMigrateTest extends BrowserTestBase {

  use MigrationTrait;

  protected $defaultTheme = 'classy';
   /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'spoonacular',
    'taxonomy',
    'menu_ui',
    'migrate_plus'
  ];
  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $user = $this->drupalCreateUser(['create recipe content']);
    $this->updateMigrationConfig();
    $this->drupalLogin($user);
  }

  public function testDataExist() {
    $data = $this->getDataJson();
    $this->assertNotEmpty($data);
  }

  /**
   * Make sure the site still works. For now just check the front page.
   */
  public function testMigrateContent() {
    $migration_id = 'recipe_node';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }
  
  public function testRecipes() {    
    $this->drupalGet('/recipes');
    $this->assertSession()->statusCodeEquals(200);
  }

}
