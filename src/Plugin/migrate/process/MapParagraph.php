<?php

namespace Drupal\spoonacular\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\process\MigrationLookup;

/**
 * This plugin extracts attributes.
 *
 * @MigrateProcessPlugin(
 *   id = "map_paragraph",
 *   handle_multiples = TRUE
 * )
 */
class MapParagraph extends MigrationLookup {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $recipe_id = $row->getSourceProperty('id');
    if (is_array($value)) {
      foreach ($value as $ingredient) {
        $source_id = $recipe_id . '_' . preg_replace('/[^a-z]/m', '_', $ingredient['name']);
        $paragraph = parent::transform($source_id, $migrate_executable, $row, $destination_property);
        if ($paragraph) {
          $paragraph_ids[] = array_combine(['target_id', 'target_revision_id'], $paragraph);
        }
      }
    }
    return $paragraph_ids ?? [];
  }

}
