<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 *
 * @MigrateSource(
 *   id = "kgaut_paragraphs_contenus"
 * )
 */
class KgautParagraphsContenu extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('paragraphs_item', 'p')->fields('p');
    $query->condition('p.bundle', 'contenu');

    $query->leftJoin('field_data_field_wysiwyg', 'fw', 'p.item_id = fw.entity_id AND fw.entity_type = \'paragraphs_item\' AND fw.deleted = 0');
    $query->fields('fw', ['field_wysiwyg_value']);

    $query->leftJoin('field_data_field_wysiwyg', 'fw', 'p.item_id = fw.entity_id AND fw.entity_type = \'paragraphs_item\' AND fw.deleted = 0');
    $query->fields('fw', ['field_wysiwyg_value']);

    $query->leftJoin('field_data_title_field', 'ft', 'p.item_id = ft.entity_id AND ft.entity_type = \'paragraphs_item\' AND ft.deleted = 0');
    $query->addField('ft', 'title_field_value', 'title');

    $query->leftJoin('field_data_field_article_image', 'fai', 'p.item_id = fai.entity_id AND fai.entity_type = \'paragraphs_item\' AND fai.deleted = 0');
    $query->addField('fai', 'field_article_image_fid', 'image_fid');
    $query->addField('fai', 'field_article_image_alt', 'image_alt');
    $query->addField('fai', 'field_article_image_title', 'image_title');
    $query->addField('fai', 'field_article_image_width', 'image_width');
    $query->addField('fai', 'field_article_image_height', 'image_height');

    $query->orderBy('p.item_id');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['item_id']['type'] = 'integer';
    return $ids;
  }

}
