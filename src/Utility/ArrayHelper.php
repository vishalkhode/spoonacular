<?php

namespace Drupal\spoonacular\Utility;

class ArrayHelper {

  /**
   * 
   * @param array $array
   * @param string $key
   */
  public static function filterDuplicates(array $array, string $unique_key) {
    $ids = array_column($array, $unique_key);
    $ids = array_unique($ids);
    $returnData = array_filter($array, function ($key, $value) use ($ids) {
        return in_array($value, array_keys($ids));
    }, ARRAY_FILTER_USE_BOTH);
    return array_values($returnData);
  }

  /**
   * 
   * @param string $uri
   * @param array $params
   */
  public static function createUrl(string $uri, array $params) {
    $invalidParam = [];
    $url = $uri;
    if (strpos($uri, ':') !== false) {
      $urlParts = explode('/', $uri);
      $urlParts = array_filter($urlParts);
      $url = [];
      foreach($urlParts as $urlPart) {
        if (strpos($urlPart, ':') !== false) {
          $param = ltrim($urlPart, ':');
          if (!isset($params[$param])) {
            $invalidParam[] = $param;
          } else {
            $urlPart = $params[$param];
            unset($params[$param]);
          }
        }
        $url[] = $urlPart;
      }
      $url = '/' . implode($url, '/');
    }
    if ($params) {
      $url .= '?' . http_build_query($params);
    }
    return [
      'url' => $url, 
      'invalidParam' => $invalidParam
    ];
  }

}