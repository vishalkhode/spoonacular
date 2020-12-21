<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\spoonacular;

use Drupal\Component\Serialization\Json;
use Drupal\spoonacular\Api\RecipeApi;
use Drupal\spoonacular\Api\RecipeApiException;
use Drupal\Core\Site\Settings;
use Drupal\Core\Http\ClientFactory;
use Psr\Log\LoggerInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\spoonacular\Utility\ArrayHelper;
use Drupal\Core\Config\ConfigFactory;

/**
 * Description of SpoonacularClient
 *
 * @author vishalkhode
 */
class SpoonacularClient {

  use MessengerTrait;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   *
   * @var type 
   */
  protected $baseUrl = "https://api.spoonacular.com/";
  
  protected $recipe_file_path = "private://recipe.json";

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * CatFactsClient constructor.
   *
   * @param $http_client_factory \Drupal\Core\Http\ClientFactory
   */
  public function __construct(ClientFactory $http_client_factory, LoggerInterface $logger, ConfigFactory $config_factory) {
    $this->client = $http_client_factory->fromOptions([
      'http_errors' => FALSE,
      'verify' => true,
      'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ]
    ]);
    $this->spoonacularConfig = $config_factory->get('spoonacular.settings');
    $this->logger = $logger;
  }

  /**
   * Get some random cat facts.
   *
   * @param int $amount
   *
   * @return array
   */
  public function request(string $api, array $params = [], $mock = FALSE) {
    $endpoint = RecipeApi::getApiEndpoint($api, $mock);
    $baseUri = RecipeApi::getBaseUrl($mock);
    try {
      $apiKey = Settings::get('spoonacular.apiKey');
      if (!$mock && empty($apiKey)) {
        throw new RecipeApiException('Api key is not added. Add the API key to $settings[\'spoonacular.apiKey\'] in your site\'s settings.php file.');
        return NULL;  
      }
      if (!$mock) {
        $params['apiKey'] = $apiKey;
      }
      switch ($endpoint['method']) {
        case 'GET':
          $url = ArrayHelper::createUrl($endpoint['uri'], $params);
          if (($invalidParam = $url['invalidParam'])) {
            throw new RecipeApiException(implode(',', $invalidParam) . ' params are required for endpoint: ' . $url['url']);
            return NULL;
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
   * 
   * @return type
   * @throws RecipeApiException
   */
  public function generateRecipes() {
    $mock = $this->spoonacularConfig->get('use_mock') ?? FALSE;
    $params = [];
    if (!$mock) {
      $apiKey = Settings::get('spoonacular.apiKey');
      $params = [
        'number' => '10',
        'cuisine' => $this->spoonacularConfig->get('default_categories') ?? 'Indian,Chinese,Italian,Mexican',
      ];
    }
    $recipes = $this->request('search_recipe', $params, $mock);
     if ($recipes) {
       foreach($recipes['results'] as $index => $recipe) {
         $recipeInformation = $this->request('recipe_information', [
           'id' => $recipe['id'],
         ], $mock);
         if ($recipeInformation) {
           $recipes['results'][$index]['recipe'] = $recipeInformation;
           $videoInformation = $this->request('video_search', [
             'id' => $recipe['id'],
             'number' => 1,
             'cuisine' => implode($recipeInformation['cuisines'], ',')
           ], $mock);
           if ($videoInformation) {
             $recipes['results'][$index]['recipe']['videos'] = $videoInformation['videos'];
           }
         }
       }
        $source_data = json_encode($recipes);
     if (file_exists($this->recipe_file_path)) {
       unlink($this->recipe_file_path);
     }
     file_save_data($source_data, $this->recipe_file_path);
     $generated = TRUE;
     }
    return $generated ?? FALSE;
  }
}
