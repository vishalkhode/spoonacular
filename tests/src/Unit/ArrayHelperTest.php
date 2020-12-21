<?php

namespace Drupal\Tests\spoonacular\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\spoonacular\Utility\ArrayHelper;

/**
 * @group spoonacular
 */
class ArrayHelperTest extends UnitTestCase {

  /**
   * Tests the array which remove duplicates for given key.
   *
   * @param array $actual
   *   Array which might contain duplicate data for given key.
   * @param array $expected
   *   Array which does not contain duplicate data for given key.
   *
   * @see Drupal\spoonacular\Plugin\migrate_plus\data_parser\CuisineJson::getSourceData().
   * @see Drupal\spoonacular\Plugin\migrate_plus\data_parser\FileJson::getSourceData().
   * @see Drupal\spoonacular\Plugin\migrate_plus\data_parser\IngredientJson::getSourceData().
   * @see Drupal\spoonacular\Plugin\migrate_plus\data_parser\RecipeJson::getSourceData().
   * 
   * @dataProvider providerTestArrayFilter
   */
  public function testArrayFilter($actual, $expected) {
    $this->assertEquals($expected, ArrayHelper::filterDuplicates($actual, 'unique'));
  }

  /**
   * Data provider for providerTestArrayFilter().
   *
   * @return array
   */
  public function providerTestArrayFilter() {
    $test_parameters = [];    
    $test_parameters[] = [
      'actual' => [
        [
          'unique' => 'abcd@!',
          'id' => 1,
        ],
        [
          'unique' => 'abcd@!',
          'id' => 2,
        ],
        [
          'unique' => 'xyzu',
          'id' => 2,
        ],
      ],
      'expected'=> [
        [
          'unique' => 'abcd@!',
          'id' => 1,
        ],
        [
          'unique' => 'xyzu',
          'id' => 2,
        ]
      ]
    ];
    return $test_parameters;
  }

 /**
   * Tests the Url generation helper method.
   *
   * @param array $actual
   *   Url which needs to be build from uri.
   * @param array $expected
   *   The array of url & invalid param.
   *
   * @see Drupal\spoonacular\SpoonacularClient::request()
   *
   * @dataProvider providerTestUrlGeneration
   */
  public function testUrlGeneration($actual, $expected) {
    $this->assertEquals($expected, ArrayHelper::createUrl($actual[0], $actual[1]));
  }

  /**
   * Data provider for testUrlGeneration().
   *
   * @return array
   */
  public function providerTestUrlGeneration() {
    $test_parameters = [];
    $test_parameters[] = [
      'actual' => ['/some/path', ['param1' => 1, 'param2' => 2]],
      'expected' => [
        'url' => '/some/path?param1=1&param2=2',
        'invalidParam'=> []
      ],
    ];

    $test_parameters[] = [
      'actual' => ['/some/:id', ['id' => 1, 'param2' => 2]],
      'expected' => [
        'url' => '/some/1?param2=2',
        'invalidParam'=> []
      ],
    ];

    $test_parameters[] = [
      'actual' => ['/some/:id/path', ['param2' => 2]],
      'expected' => [
        'url' => '/some/:id/path?param2=2',
        'invalidParam'=> ['id']
      ],
    ];
    return $test_parameters; 
  }

}