<?php

namespace Drupal\spoonacular\Commands;

use Drupal\migrate_tools\Commands\MigrateToolsCommands;
use Drupal\spoonacular\SpoonacularClient;
use Drupal\Core\Config\ConfigFactory;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Spoonacular drush commands.
 */
class SpoonacularCommands extends MigrateToolsCommands {

  use StringTranslationTrait;

  /**
   * MigrateToolsCommands constructor.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManager $migrationPluginManager
   *   Migration Plugin Manager service.
   * @param \Drupal\Core\Datetime\DateFormatter $dateFormatter
   *   Date formatter service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $keyValue
   *   Key-value store service.
   * @param Drupal\Core\Config\ConfigFactory $config_factory
   *   Config Factory interface.
   * @param Drupal\spoonacular\SpoonacularClient $spoonacularClient
   *   Config Factory interface.
   */
  public function __construct(MigrationPluginManager $migrationPluginManager, DateFormatter $dateFormatter, EntityTypeManagerInterface $entityTypeManager, KeyValueFactoryInterface $keyValue, ConfigFactory $config_factory, SpoonacularClient $spoonacularClient) {
    parent::__construct($migrationPluginManager, $dateFormatter, $entityTypeManager, $keyValue);
    $this->spoonacularClient = $spoonacularClient;
    $this->spoonacularConfig = $config_factory->get('spoonacular.settings');
  }

  /**
   * Migrate content for the Spoonacular API.
   *
   * @command spoonacular:migrate
   *
   * @usage spoonacular:migrate
   *   Retrieve status for all migrations
   *
   * @validate-module-enabled migrate_tools
   *
   * @aliases spoonacular-migrate, migrate-recipes
   */
  public function migrate() {
    $useMock = $this->spoonacularConfig->get('use_mock') ?? TRUE;
    $apiName = ($useMock) ? "Mock" : "Spoonacular";
    $status = $this->t('Fetching Recipes from the @apiName API.', ['@apiName' => $apiName]);
    $this->logger()->notice($status);
    if ($this->spoonacularClient->generateRecipes()) {
      $this->logger()->notice($this->t('Migrating Recipes.'));
      $this->import('', ['group' => 'recipe', 'update' => TRUE]);
    }
  }

}
