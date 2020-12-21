<?php

namespace Drupal\spoonacular\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use \Drupal\file\Entity\File;

/**
 * This plugin extracts attributes.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_value",
 *   handle_multiples = TRUE
 * )
 */
class ExtractValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    //print_r($row->getDestinationProperty('_youtubeId')); 
    //die;
    if (is_array($value)) {
      $new_value = array_column($value, $this->configuration['index']);
    }
    return $new_value ?? [];
  }
}
