<?php
/**
 * @file
 * Contains \Drupal\kgaut_migrate\Plugin\migrate\source\Term_Tags.
 */

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Taxonomy: Tags.
 *
 * @MigrateSource(
 *   id = "snippet_tags_migration"
 * )
 */
abstract class TaxonomyTermMigrateSource extends SqlBase {

  protected static $vocabulary_id;

  public function query() {
    $query = $this->select('taxonomy_term_data', 'td');
    $query->fields('td', ['tid', 'name', 'description', 'weight']);
    $query->condition('td.vid', static::$vocabulary_id);
    //permet d'importer que les termes utilisés
    $query->join('taxonomy_index', 'ti', 'ti.tid = td.tid');
    $query->orderBy('td.name', 'ASC');
    return $query;
  }

  public function fields() {
    return [
      'name' => $this->t('name'),
      'description' => $this->t('Description'),
      'weight' => $this->t('Weight'),
    ];
  }

  public function getIds() {
    return [
      'tid' => [
        'type' => 'string',
        'alias' => 'td',
      ],
    ];
  }
}