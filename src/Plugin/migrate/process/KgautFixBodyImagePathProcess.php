<?php

namespace Drupal\kgaut_migrate\Plugin\migrate\process;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "kgaut_fix_body_image_path_process"
 * )
 */
class KgautFixBodyImagePathProcess extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($html, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Values for the following variables are specified in the YAML file above.
    $images_source = $this->configuration['images_source'];
    $destination = $this->configuration['images_destination'];
    $url_source = $this->configuration['url_source'];

    preg_match_all('/<img[^>]+>/i', $html, $result);

    if (!empty($result[0])) {

      foreach ($result as $img_tags) {
        foreach ($img_tags as $img_tag) {

          preg_match_all('/(alt|title|src)=("[^"]*")/i', $img_tag, $tag_attributes);

          $filepath = str_replace('"', '', $tag_attributes[2][1]);

          if (!empty($tag_attributes[2][1])) {

            // Create file object from a locally copied file.
            $filename = basename($filepath);
            $destination_finale = $destination . 'node-' . $row->getSourceProperty('nid');
            if (file_prepare_directory($destination_finale, FILE_CREATE_DIRECTORY)) {

              if (filter_var($filepath, FILTER_VALIDATE_URL)) {
                $file_contents = file_get_contents($filepath);
              }
              else {
                $file_contents = file_get_contents($url_source . $filepath);
              }
              $new_destination = $destination_finale . '/' . $filename;

              if (!empty($file_contents)) {

                if ($file = file_save_data($file_contents, $new_destination, FILE_EXISTS_REPLACE)) {
                  $uri_destination = str_replace('public://', '/' . PublicStream::basePath() . '/', $new_destination);
                  $html = str_replace($filepath, $uri_destination, $html);
                }
              }
            }
          }
        }
      }
    }
    return $html;
  }

}
