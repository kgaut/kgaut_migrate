<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for beer content.
 *
 * @MigrateSource(
 *   id = "page_node"
 * )
 */
class PageNodeMigrateSource extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    /**
     * An important point to note is that your query *must* return a single row
     * for each item to be imported. Here we might be tempted to add a join to
     * migrate_example_beer_topic_node in our query, to pull in the
     * relationships to our categories. Doing this would cause the query to
     * return multiple rows for a given node, once per related value, thus
     * processing the same node multiple times, each time with only one of the
     * multiple values that should be imported. To avoid that, we simply query
     * the base node data here, and pull in the relationships in prepareRow()
     * below.
     */
    $query = $this->select('node', 'n');
    $query->condition('n.type', 'page');
    $query->fields('n', ['nid', 'vid', 'title', 'status', 'created', 'changed', 'promote', 'sticky', 'uid']);
    $query->leftJoin('field_data_body', 'fdb', 'fdb.entity_id = n.nid AND fdb.entity_type = \'node\' AND fdb.deleted = 0');
    $query->fields('fdb', ['body_value', 'body_summary']);


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
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  public function prepareRow(Row $row) {
    $query = $this->select('field_data_field_contenu', 'fdc');
    $query->condition('fdc.entity_id', $row->getSourceProperty('nid'));
    $query->condition('fdc.bundle', 'page');
    $query->addField('fdc', 'field_contenu_value', 'paragraph_value');
    $query->addField('fdc', 'field_contenu_revision_id', 'paragraph_revision_id');
    $query->addField('fdc', 'delta', 'paragraph_delta');
    $query->orderBy('fdc.delta');
    $paragraphs = $query->execute()->fetchAllAssoc('paragraph_value');
    dd($paragraphs);
    $row->setSourceProperty('paragraph_items', $paragraphs);

    return parent::prepareRow($row);
  }

}
