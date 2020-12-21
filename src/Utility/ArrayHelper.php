<?php

namespace Drupal\spoonacular\Utility;

/**
 * Class ArrayHelper for perform different operations.
 */
class ArrayHelper {

  /**
   * Filters the duplicate entries in array.
   *
   * @param array $array
   *   An input array.
   * @param string $unique_key
   *   Key which needs to look for filtering.
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
   * Creates an uri with query parameters by replacing parameter placeholders.
   *
   * @param string $uri
   *   Input Uri.
   * @param array $params
   *   An array of parameters.
   */
  public static function createUrl(string $uri, array $params) {
    $invalidParam = [];
    $url = $uri;
    if (strpos($uri, ':') !== FALSE) {
      $urlParts = explode('/', $uri);
      $urlParts = array_filter($urlParts);
      $url = [];
      foreach ($urlParts as $urlPart) {
        if (strpos($urlPart, ':') !== FALSE) {
          $param = ltrim($urlPart, ':');
          if (!isset($params[$param])) {
            $invalidParam[] = $param;
          }
          else {
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
      'invalidParam' => $invalidParam,
    ];
  }

}
