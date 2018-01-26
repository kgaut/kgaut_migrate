<?php

namespace Drupal\kgaut_migrate;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class MigrateManager.
 */
class MigrateManager {

  public function migrateSnippetTags() {
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', 'snippets_tags');
    $term_ids = $query->execute();
    $terms = Term::loadMultiple($term_ids);
    foreach ($terms as $term) {
      $termCible = taxonomy_term_load_multiple_by_name($term->label(), 'tags');
      if (count($termCible) > 0) {
        $termCible = array_pop($termCible);
      }
      else {
        $termCible = Term::create([
          'vid' => 'tags',
          'name' => $term->label(),
        ]);
        $termCible->save();
      }
      $query = \Drupal::entityQuery('node');
      $query->condition('field_tags', $term->id(), 'IN');
      $query->condition('type', 'snippet');

      $noeuds_ids = $query->execute();
      $nbNoeudsModifies = 0;
      $noeuds = Node::loadMultiple($noeuds_ids);
      foreach ($noeuds as $noeud) {
        $tags = $noeud->field_tags->getValue();
        $new_tags = [];
        $new_tags[] = ['target_id' => (int) $termCible->id()];
        foreach ($tags as $value) {
          if($value['target_id'] != $termCible->id()) {
            $new_tags[] = ['target_id' => (int) $value['target_id']];
          }
        }
        $noeud->field_tags->setValue($new_tags);
        $noeud->save();
        $nbNoeudsModifies++;
      }
      drupal_set_message('Term ' . $term->label() . ', ' . $nbNoeudsModifies . ' noeuds modifiés');
      $term->delete();
    }
  }
}
