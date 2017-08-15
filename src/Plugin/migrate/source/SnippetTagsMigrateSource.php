<?php
/**
 * @file
 * Contains \Drupal\kgaut_migrate\Plugin\migrate\source\Term_Tags.
 */

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

/**
 * Taxonomy: Tags.
 *
 * @MigrateSource(
 *   id = "snippet_tags_migration"
 * )
 */
class SnippetTagsMigrateSource extends KgautTaxonomyTermMigrateSource {

  protected static $vocabulary_id = 7;

}