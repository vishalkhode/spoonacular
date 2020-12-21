<?php

namespace Drupal\spoonacular;

use Drupal\Component\Serialization\Json;
use Drupal\spoonacular\Api\RecipeApi;
use Drupal\spoonacular\Api\RecipeApiException;
use Drupal\Core\Site\Settings;
use Drupal\Core\Http\ClientFactory;
use Psr\Log\LoggerInterface;
use Drupal\spoonacular\Utility\ArrayHelper;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * SpoonacularClient class to make API calls to Spoonacular.
 */
class SpoonacularClient {

  use StringTranslationTrait;

  /**
   * Recipe json file path used for migration purpose.
   */
  const RECIPE_FILE_PATH = "private://recipe.json";

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The GuzzleHttp client object.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * SpoonacularClient constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $http_client
   *   Http Client Factory.
   * @param Psr\Log\LoggerInterface $logger
   *   Logger interface.
   * @param Drupal\Core\Config\ConfigFactory $config
   *   Config Factory interface.
   */
  public function __construct(ClientFactory $http_client, LoggerInterface $logger, ConfigFactory $config) {
    $this->client = $http_client->fromOptions([
      'http_errors' => FALSE,
      'verify' => TRUE,
      'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ],
    ]);
    $this->spoonacularConfig = $config->get('spoonacular.settings');
    $this->logger = $logger;
  }

  /**
   * Make an API call with given parameters.
   *
   *   - API name, urls & methods are defined in RecipeApi::getApiEndpoint().
   *   - Based on module config, get the base Uri.i.e Spoonacular or Mock API.
   *
   * @param string $api
   *   API name whose mapping defined in RecipeApi::getApiEndpoint().
   * @param array $params
   *   An array of query or parameters to pass along with API.
   * @param bool $mock
   *   Boolean for Spoonacular or Mock API.
   *
   * @return array
   *   Returns an array of Recipe JSON returned by API.
   *
   * @throws \Drupal\spoonacular\Api\RecipeApiException
   */
  public function request(string $api, array $params = [], $mock = FALSE) {
    $endpoint = RecipeApi::getApiEndpoint($api, $mock);
    $baseUri = RecipeApi::getBaseUrl($mock);
    try {
      $apiKey = Settings::get('spoonacular.apiKey');
      if (!$mock && empty($apiKey)) {
        throw new RecipeApiException('Api key is not added. Add the API key to $settings[\'spoonacular.apiKey\'] in your site\'s settings.php file.');
      }
      if (!$mock) {
        $params['apiKey'] = $apiKey;
      }
      switch ($endpoint['method']) {
        case 'GET':
          $url = ArrayHelper::createUrl($endpoint['uri'], $params);
          if (($invalidParam = $url['invalidParam'])) {
            $exception = implode(',', $invalidParam) . ' params are required for endpoint: ' . $url['url'];
            throw new RecipeApiException($exception);
          }
          $response = $this->client->get($baseUri . $url['url']);
          $status_code = $response->getStatusCode();
          break;

        case 'POST':
          $response = $this->client->post($baseUri . $endpoint['uri'], $params);
          $status_code = $response->getStatusCode();
          break;
      }
      $data = Json::decode($response->getBody());
      if ($status_code < 200 || $status_code > 299) {
        throw new RecipeApiException($data['message']);
      }
    }
    catch (RecipeApiException $e) {
      $this->logger->error($e->getMessage());
      return NULL;
    }
    return $data;
  }

  /**
   * Generates Recipe Json file by making an API call and store it on server.
   *
   * @return bool
   *   Returns true|false based on file generation.
   *
   * @throws \Drupal\spoonacular\Api\RecipeApiException
   */
  public function generateRecipes() {
    $mock = $this->spoonacularConfig->get('use_mock') ?? FALSE;
    $params = [];
    if (!$mock) {
      $params = [
        'number' => '10',
        'cuisine' => $this->spoonacularConfig->get('default_categories') ?? 'Indian,Chinese,Italian,Mexican',
      ];
    }
    $recipes = $this->request('search_recipe', $params, $mock);
    if ($recipes) {
      foreach ($recipes['results'] as $index => $recipe) {
        $recipeInformation = $this->request('recipe_information', [
          'id' => $recipe['id'],
        ], $mock);
        if ($recipeInformation) {
          $recipes['results'][$index]['recipe'] = $recipeInformation;
          $videoInformation = $this->request('video_search', [
            'id' => $recipe['id'],
            'number' => 1,
            'cuisine' => implode($recipeInformation['cuisines'], ','),
          ], $mock);
          if ($videoInformation) {
            $recipes['results'][$index]['recipe']['videos'] = $videoInformation['videos'];
          }
        }
      }
      $source_data = json_encode($recipes);
      if (file_exists(self::RECIPE_FILE_PATH)) {
        unlink(self::RECIPE_FILE_PATH);
      }
      file_save_data($source_data, self::RECIPE_FILE_PATH);
      $generated = TRUE;
    }
    return $generated ?? FALSE;
  }

}
