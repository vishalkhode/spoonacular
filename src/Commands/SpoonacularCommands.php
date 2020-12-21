<?php

namespace Drupal\spoonacular\Commands;

use Drupal\migrate_tools\Commands\MigrateToolsCommands;
use Drupal\spoonacular\SpoonacularClient;
use Drupal\Core\Config\ConfigFactory;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\spoonacular\Api\RecipeApiException;

/**
 * Description of SpoonacularCommands
 *
 * @author vishalkhode
 */
class SpoonacularCommands extends MigrateToolsCommands {
 public function __construct(MigrationPluginManager $migrationPluginManager, DateFormatter $dateFormatter, EntityTypeManagerInterface $entityTypeManager, KeyValueFactoryInterface $keyValue, ConfigFactory $config_factory, SpoonacularClient $spoonacularClient) {
    parent::__construct($migrationPluginManager, $dateFormatter, $entityTypeManager, $keyValue);
    $this->spoonacularClient = $spoonacularClient;
    $this->spoonacularConfig = $config_factory->get('spoonacular.settings');
  }

  /**
   * Migrate content from the Spoonacular API.
   *
   * @param array $options
   *   Additional options for the command.
   *
   * @command spoonacular:migrate
   *
   * @option group A comma-separated list of migration groups to list
   * @option tag Name of the migration tag to list
   * @option names-only Only return names, not all the details (faster)
   * @option continue-on-failure When a migration fails, continue processing
   *   remaining migrations.
   *
   * @default $options []
   *
   * @usage spoonacular:migrate
   *   Retrieve status for all migrations
   *
   * @validate-module-enabled migrate_tools
   *
   * @aliases spoonacular-migrate, migrate-recipes
   *
   *
   */
  public function migrate() {
    $useMock = $this->spoonacularConfig->get('use_mock') ?? TRUE;
    $apiName = ($useMock) ? "Mock" : "Spoonacular";
    $status = t('Fetching Recipes from the @apiName API.', ['@apiName' => $apiName]);
    $this->logger()->notice($status);
    if ($this->spoonacularClient->generateRecipes()) {
      $this->logger()->notice('Migrating Recipes.');
      $this->import('', ['group' => 'recipe', 'update' => TRUE]);
    }
  }

}
