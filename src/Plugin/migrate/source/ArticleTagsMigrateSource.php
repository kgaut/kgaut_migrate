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
 *   id = "article_tags_migration"
 * )
 */
class ArticleTagsMigrateSource extends SqlBase {

  protected static $vocabulary_id = 3;

  public function query() {
    $query = $this->select('taxonomy_term_data', 'td');
    $query->fields('td', ['tid', 'name', 'description', 'weight']);
    $query->condition('td.vid', static::$vocabulary_id);
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