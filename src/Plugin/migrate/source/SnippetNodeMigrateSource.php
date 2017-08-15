<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for beer content.
 *
 * @MigrateSource(
 *   id = "snippet_node"
 * )
 */
class SnippetNodeMigrateSource extends SqlBase {

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
    $query->condition('n.type', 'snippets');
    $query->fields('n', ['nid', 'title', 'status', 'created', 'changed', 'promote', 'sticky', 'uid']);
    $query->join('field_data_body', 'fdb', 'fdb.entity_id = n.nid AND entity_type = \'node\' AND deleted = 0');
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
    /**
     * As explained above, we need to pull the style relationships into our
     * source row here, as an array of 'style' values (the unique ID for
     * the beer_term migration).
     */
    $query = $this->select('field_data_field_snippet_tags', 'ft');
    $query->addField('ft', 'field_snippet_tags_tid', 'tid');
    $query->condition('entity_id', $row->getSourceProperty('nid'));
    $query->condition('bundle', 'snippets');
    $terms = $query->execute()->fetchCol();
    $row->setSourceProperty('tags', $terms);
    return parent::prepareRow($row);
  }

}
