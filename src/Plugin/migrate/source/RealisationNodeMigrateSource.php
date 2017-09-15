<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for beer content.
 *
 * @MigrateSource(
 *   id = "realisation_node"
 * )
 */
class RealisationNodeMigrateSource extends SqlBase {

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
    $query->condition('n.type', 'realisation');
    $query->fields('n', ['nid', 'vid', 'title', 'status', 'created', 'changed', 'promote', 'sticky', 'uid']);
    $query->leftJoin('field_data_body', 'fdb', 'fdb.entity_id = n.nid AND fdb.entity_type = \'node\' AND fdb.deleted = 0');
    $query->fields('fdb', ['body_value', 'body_summary']);
    $query->leftJoin('field_data_field_year', 'fy', 'fy.entity_id = n.nid AND fy.entity_type = \'node\' AND fy.deleted = 0');
    $query->addField('fy','field_year_value','realisation_date');

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
    //merge des deux champs
    $query = $this->select('field_data_field_realisation_missions', 'frm');
    $query->condition('frm.entity_id', $row->getSourceProperty('nid'));
    $query->condition('frm.bundle', 'realisation');
    $query->leftJoin('field_data_field_realisation_features', 'frf', 'frf.entity_id = frm.entity_id AND frf.entity_type = \'node\'');

    $query->fields('frm', ['field_realisation_missions_value']);
    $query->fields('frf', ['field_realisation_features_value']);
    $missions = $query->execute()->fetchAssoc();

    $missions = '<h3>Missions</h3>' . $missions['field_realisation_missions_value'] . '<h3>Caractéristiques</h3>' . $missions['field_realisation_features_value'];

    $row->setSourceProperty('missions', $missions);

    /**
     * As explained above, we need to pull the style relationships into our
     * source row here, as an array of 'style' values (the unique ID for
     * the beer_term migration).
     */
    $query = $this->select('field_data_field_realisation_tag', 'ft');
    $query->addField('ft', 'field_realisation_tag_target_id', 'tid');
    $query->condition('entity_id', $row->getSourceProperty('nid'));
    $query->condition('bundle', 'realisation');
    $terms = $query->execute()->fetchCol();
    $row->setSourceProperty('tags', $terms);

    $date = \DateTime::createFromFormat('U', $row->getSourceProperty('realisation_date'));
    if($date) {
      $date->setTimezone(new \DateTimeZone('Europe/Paris'));
      $row->setSourceProperty('realisation_date', $date->format('Y-m-d'));
    }
    else {
      $row->setSourceProperty('realisation_date', NULL);
    }

    //récupération des screenshots
    $query = $this->select('field_data_field_screenshot', 'fs');
    $query->condition('entity_id', $row->getSourceProperty('nid'));
    $query->condition('bundle', 'realisation');
    $query->addField('fs', 'delta', 'delta');
    $query->addField('fs', 'field_screenshot_fid', 'fid');
    $query->addField('fs', 'field_screenshot_alt', 'alt');
    $query->addField('fs', 'field_screenshot_title', 'title');
    $query->addField('fs', 'field_screenshot_width', 'width');
    $query->addField('fs', 'field_screenshot_height', 'height');
    $files = $query->execute()->fetchAllAssoc('fid');
    $row->setSourceProperty('screenshots', $files);

    //récupération des URLS
    $query = $this->select('field_data_field_url', 'fu');
    $query->condition('entity_id', $row->getSourceProperty('nid'));
    $query->condition('bundle', 'realisation');
    $query->addField('fu', 'field_url_url', 'url');
    $query->addField('fu', 'field_url_title', 'title');
    $query->addField('fu', 'delta', 'delta');
    $urls = $query->execute()->fetchAllAssoc('delta');
    $row->setSourceProperty('url', $urls);

    return parent::prepareRow($row);
  }

}
