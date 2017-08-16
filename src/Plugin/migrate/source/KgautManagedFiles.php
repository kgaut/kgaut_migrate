<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * @MigrateSource(
 *   id = "kgaut_managed_files"
 * )
 */
class kgautManagedFiles extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('file_managed', 'fm');
    $query->fields('fm', ['fid', 'uid', 'filename', 'uri', 'filemime', 'filesize', 'status', 'origname', 'type']);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('ID'),
      'title' => $this->t('Title'),
      'body' => $this->t('body'),
      'excerpt' => $this->t('Résumé'),
      'uid' => $this->t('Account ID of the author'),
      'tags' => $this->t("Tags de l'article"),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
        'alias' => 'fm',
      ],
    ];
  }

  public function prepareRow(Row $row) {
    return parent::prepareRow($row);
  }

}
