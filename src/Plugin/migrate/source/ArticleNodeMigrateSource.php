<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for beer content.
 *
 * @MigrateSource(
 *   id = "article_node"
 * )
 */
class ArticleNodeMigrateSource extends SqlBase {

  /**
   * La fonction « query » ne doit retourner qu'une seule ligne par contenu à
   * importer (ici un noeud).
   * Le champ « tags » n'est donc pas importé ici car tout contenu avec deux
   * tags créerai automatiquement deux lignes. Pour récupérer les tags, la
   * méthode « prepareRow » est utilisée (voir plus pas).
   */
  public function query() {
    $query = $this->select('node', 'n');
    $query->condition('n.type', 'story');
    $query->fields('n', ['nid', 'vid', 'title', 'status', 'created', 'changed', 'promote', 'sticky', 'uid']);
    $query->leftJoin('field_data_body', 'fdb', 'fdb.entity_id = n.nid AND fdb.entity_type = \'node\' AND fdb.deleted = 0');
    $query->fields('fdb', ['body_value', 'body_summary']);
    $query->leftJoin('field_data_field_article_image', 'fai', 'fai.entity_id = n.nid AND fai.entity_type = \'node\' AND fai.deleted = 0');
    $query->fields('fai', ['field_article_image_fid', 'field_article_image_alt', 'field_article_image_title', '	field_article_image_width', 'field_article_image_height']);

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
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * Cette methode sert à effectuer des traitements sur chaque ligne récupérée
   * par la méthode query(). Comme expliqué plus haut, on s'en sert ici pour
   * récupérer les tags des contenus (champs multiple).
   */
  public function prepareRow(Row $row) {
    $query = $this->select('field_data_taxonomy_vocabulary_3', 'ft');
    $query->addField('ft', 'taxonomy_vocabulary_3_tid', 'tid');
    $query->condition('entity_id', $row->getSourceProperty('nid'));
    $query->condition('bundle', 'story');
    $terms = $query->execute()->fetchCol();
    $row->setSourceProperty('tags', $terms);
    return parent::prepareRow($row);
  }

}
