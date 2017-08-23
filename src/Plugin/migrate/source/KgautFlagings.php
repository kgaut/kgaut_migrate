<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 *
 * @MigrateSource(
 *   id = "kgaut_flagings"
 * )
 */
class KgautFlagings extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('flagging', 'f')->fields('f');
    $query->orderBy('f.timestamp');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['flagging_id']['type'] = 'integer';
    return $ids;
  }

}
