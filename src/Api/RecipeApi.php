<?php

namespace Drupal\spoonacular\Api;

class RecipeApi {
  
  public static function getBaseUrl($mock) {
    if (!$mock) {
      return "https://api.spoonacular.com";
    } else {
      return "https://195z2rck80.execute-api.us-east-1.amazonaws.com";
    }
  }

  /**
   * 
   * @return type
   */
  protected static function apiEndpoints() {
    return [
      'search_recipe' => [
        'method' => 'GET',
        'uri' => '/recipes/complexSearch'
      ],
      'recipe_information' => [
        'method' => 'GET',
        'uri' => '/recipes/:id/information'
      ],
      'video_search' => [
        'method' => 'GET',
        'uri' => '/food/videos/search'
      ],
    ];
  }
  
  protected static function mockApiEndpoints() {
    return [
      'search_recipe' => [
        'method' => 'GET',
        'uri' => '/recipes/complexSearch'
      ],
      'recipe_information' => [
        'method' => 'GET',
        'uri' => '/recipes/:id/information'
      ],
      'video_search' => [
        'method' => 'GET',
        'uri' => '/food/videos/:id'
      ],
    ];
  }

  /**
   * 
   * @param type $type
   * @return type
   * @throws RecipeApiException
   */
  public static function getApiEndpoint($type, $mock = FALSE) {
    if (!$mock) {
      $endpoints = self::apiEndpoints();
    } else {
       $endpoints = self::mockApiEndpoints();
    }
    if (!isset($endpoints[$type])) {
      $error = "Invalid request or API endpoint doesn't exist: " . $type;
      throw new RecipeApiException($error);
    }
    return $endpoints[$type]; 
  }

}