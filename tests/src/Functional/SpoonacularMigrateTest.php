<?php

namespace Drupal\Tests\spoonacular\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\Tests\spoonacular\Traits\MigrationTrait;

/**
 * Functional unit test to verify recipe migration.
 *
 * @group spoonacular
 */
class SpoonacularMigrateTest extends BrowserTestBase {

  use MigrationTrait;

  /**
   * DefaultTheme to install.
   *
   * @var string
   */
  protected $defaultTheme = 'classy';

  /**
   * Use Standard profile.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'spoonacular',
    'taxonomy',
    'menu_ui',
    'migrate_plus',
    'media',
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

  /**
   * Tests sample recipe data content.
   */
  public function testDataExist() {
    $data = $this->getDataJson();
    $this->assertNotEmpty($data);
  }

  /**
   * Verify if recipe content is migrated.
   */
  public function testMigrateContent() {
    $migration_id = 'recipe_node';
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);
    $migration->getIdMap()->prepareUpdate();
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

  /**
   * Tests the recipe listing page.
   */
  public function testRecipes() {
    $this->drupalGet('/recipes');
    $this->assertSession()->statusCodeEquals(200);
  }

}
