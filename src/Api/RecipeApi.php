<?php

namespace Drupal\spoonacular\Api;

/**
 * RecipeApi class to define all endpoints and method.
 */
class RecipeApi {

  /**
   * Gets the API base URL.
   *
   * @param bool $mock
   *   Defines which API base URL needed.
   *
   * @return string
   *   Returns API base URL.
   */
  public static function getBaseUrl($mock) {
    if (!$mock) {
      return "https://api.spoonacular.com";
    }
    else {
      return "https://195z2rck80.execute-api.us-east-1.amazonaws.com";
    }
  }

  /**
   * Predefined mapping of all API uri, method with some defined name.
   *
   * @return array
   *   An array of all Spoonacular API endpoints.
   */
  protected static function apiEndpoints() {
    return [
      'search_recipe' => [
        'method' => 'GET',
        'uri' => '/recipes/complexSearch',
      ],
      'recipe_information' => [
        'method' => 'GET',
        'uri' => '/recipes/:id/information',
      ],
      'video_search' => [
        'method' => 'GET',
        'uri' => '/food/videos/search',
      ],
    ];
  }

  /**
   * Predefined mapping of all API uri, method with some defined name.
   *
   * @return array
   *   An array of all Mock API endpoints (Used for testing purpose).
   *  @todo we can remove this later.
   */
  protected static function mockApiEndpoints() {
    return [
      'search_recipe' => [
        'method' => 'GET',
        'uri' => '/recipes/complexSearch',
      ],
      'recipe_information' => [
        'method' => 'GET',
        'uri' => '/recipes/:id/information',
      ],
      'video_search' => [
        'method' => 'GET',
        'uri' => '/food/videos/:id',
      ],
    ];
  }

  /**
   * Function to find API uri and method.
   *
   * @param string $type
   *   String name to look for API uri.
   * @param bool $mock
   *   Defines which API base URL needed.
   *
   * @return array
   *   Returns an array of API uri and method for given name
   *
   * @throws RecipeApiException
   */
  public static function getApiEndpoint($type, $mock = FALSE) {
    if (!$mock) {
      $endpoints = self::apiEndpoints();
    }
    else {
      $endpoints = self::mockApiEndpoints();
    }
    if (!isset($endpoints[$type])) {
      $error = "Invalid request or API endpoint doesn't exist: " . $type;
      throw new RecipeApiException($error);
    }
    return $endpoints[$type];
  }

}
