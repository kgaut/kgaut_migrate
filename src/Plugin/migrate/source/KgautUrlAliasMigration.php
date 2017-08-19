<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * @MigrateSource(
 *   id = "kgaut_url_alias_migration",
 * )
 */
class KgautUrlAliasMigration extends SqlBase {


  public function query() {
    // The order of the migration is significant since
    // \Drupal\Core\Path\AliasStorage::lookupPathAlias() orders by pid before
    // returning a result. Postgres does not automatically order by primary key
    // therefore we need to add a specific order by.
    return $this->select('url_alias', 'ua')->fields('ua')->orderBy('pid');
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['pid']['type'] = 'integer';
    return $ids;
  }
  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = parent::fields();

    $fields['pid'] = $this->t('The numeric identifier of the path alias.');
    $fields['language'] = $this->t('The language code of the URL alias.');
    $fields['source'] = $this->t('The internal system path.');
    $fields['alias'] = $this->t('The path alias.');
    $fields['source'] = $this->t('The internal system path.');
    $fields['alias'] = $this->t('The path alias.');
    return $fields;
  }
}
