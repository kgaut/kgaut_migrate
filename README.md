# kgaut_migrate

tl;dr : Exemple de migration d'un site drupal 7 vers un drupal 8

## Configuration de base
Dans le cadre d'une migration de drupal => Drupal, Migrate impose d'avoir
un site qui sera la source (le site depuis duquel vous souhaitez récupérer
le contenu, ainsi qu'un site cible, dans lequel vous réijecterez le contenu.

La base de données du site source doit être accessible, ces infos de
connexions doivent être présent dans le fichier settings.php, dans le
tableau `$databases`, mais avec une clé différente de `default`.

Dans mon cas le site cible est une installation "fraiche" de drupal 8,
le site source est le site http://kgaut.net que j'ai installé en local.

Voici la définition de ma base de données « source » dans mon fichier
settings.php :

```
$databases['migrate_kgaut_d7']['default'] = $databases['default']['default'];
$databases['migrate_kgaut_d7']['default']['database'] = 'kgaut_net_d7';
$databases['migrate_kgaut_d7']['default']['prefix'] = 'kgaut_';
```

**Note : ** La clée est ici `migrate_kgaut_d7`, c'est celle qui doit être
renseignée dans le fichier définissant votre groupe de migrations, cf
`migrate_plus.migration_group.kgaut.yml`.


## Processus

À chaque fois que vous modifiez / ajoutez un fichier de configuration de
migration, vous devez réimporter la configuration du module dans le site.

Pour cela, la première solution est de réinstaller le module. Sinon vous
pouvez faire un import partiel de configuration, je privilegie la seconde
solution.

Note : nous supposerons ici que l'alias défini est @kg et que le group est
`kgaut` (oui je suis très égocentrique, mais c'est mon site après tout).

Voici donc les commandes à lancer à chaque test :

### Solution 1 : Réinstallation du module

```
drush @kg mr --group=kgaut // Rollback des migrations (suppression des contenus importés)
drush @kg pmu kgaut_migrate // Désinstallation du module
drush @kg en kgaut_migrate // installation du module
drush @kg mi --group=kgaut //Lancement des migrations
```
### Solution 2 import partiel de la configuration
```
drush @kg mr --group=kgaut
drush @kg cim --source=modules/kgaut_migrate/config/install --partial -y
drush @kg mi --group=kgaut
```

ou en une ligne :
```
drush @kg mr --group=kgaut && drush @kg cim --source=modules/kgaut_migrate/config/install --partial -y && drush @kg mi --group=kgaut
```
## Les migrations

### Divers

#### Fichiers - kgaut_managed_files
Migration des fichiers (`managed_file`),
  - Configuration : `migrate_plus.migration.kgaut_managed_files.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\KgautManagedFiles.php`
Ce script importe maintenant les fichiers.

#### Alias - kgaut_url_alias
Migration des alias d'url du module path,
  - Configuration : `migrate_plus.migration.kgaut_url_alias.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\KgautUrlAliasMigration.php`

### Termes de Taxonomy

#### kgaut_article_tags
Terms utilisés pour tagguer les contenus "Articles"
  - Configuration : `migrate_plus.migration.kgaut_article_tags.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\ArticleTagsMigrateSource.php`

#### kgaut_realisation_tags
Terms utilisés pour tagguer les contenus "Realisation"
  - Configuration : `migrate_plus.migration.kgaut_realisation_tags.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\RealisationTagsMigrateSource.php`

#### kgaut_snippet_tags
Terms utilisés pour tagguer les contenus "Tags"
  - Configuration : `migrate_plus.migration.kgaut_snippet_tags.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\SnippetTagsMigrateSource.php`

### Noeuds

#### kgaut_articles_node
Migration des noeuds du type de contenu `story` (le site est à l'origine un d6) vers `article`.
  - Configuration : `migrate_plus.migration.kgaut_articles_node.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\ArticleNodeMigrateSource.php`
  - Process :
    - `Drupal\kgaut_migrate\Plugin\migrate\process\KgautFixBodyImagePathProcess.php` :
    Le champ body est parsé pour récupérer les images insérées via IMCE,
    les rapatrier et les trasnferer dans le dossier « public:// » dans
    un sous dossier spéficique. Les fichiers sont aussi renommés au
    passage.

Particularités :
  - Import d'un champ « image » (`field_article_image` vers `field_image`)

#### kgaut_page_node
Migration des noeuds du type de contenu `page` vers `page`.
  - Configuration : `migrate_plus.migration.kgaut_page_node.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\PageNodeMigrateSource.php`
  - Process :
    - `Drupal\kgaut_migrate\Plugin\migrate\process\KgautFixBodyImagePathProcess.php` :
    Le champ body est parsé pour récupérer les images insérées via IMCE,
    les rapatrier et les trasnferer dans le dossier « public:// » dans
    un sous dossier spéficique. Les fichiers sont aussi renommés au
    passage.

#### kgaut_realisation_node
Migration des noeuds du type de contenu `realisation` vers `realisation`.
  - Configuration : `migrate_plus.migration.kgaut_realisation_node.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\RealisationNodeMigrateSource.php`
  - Process :
    - `Drupal\kgaut_migrate\Plugin\migrate\process\KgautFixBodyImagePathProcess.php` :
    Le champ body est parsé pour récupérer les images insérées via IMCE,
    les rapatrier et les trasnferer dans le dossier « public:// » dans
    un sous dossier spéficique. Les fichiers sont aussi renommés au
    passage.

#### kgaut_snippets_node
Migration des noeuds du type de contenu `snippet` vers `snippet`.
  - Configuration : `migrate_plus.migration.kgaut_snippets_node.yml`
  - Source : `Drupal\kgaut_migrate\Plugin\migrate\source\SnippetNodeMigrateSource.php`
  - Le champ body est parsé pour récupérer les images insérées via IMCE,
  les rapatrier et les trasnferer dans le dossier « public:// » dans
  un sous dossier spéficique. (voir le processeur `KgautFixBodyImagePathProcess`).

## Les commandes drush

 - `drush @kg ms` : status des migration
 - `drush @kg mi kgaut_article_tags` : lancer une migration
 - `drush @kg mi --group=kgaut` : lancer un groupe de migrations
 - `drush @kg mr kgaut_article_tags` : Annuler une migration
 - `drush @kg mr --group=kgaut` : Annuler un groupe de migrations
 - `drush @kg mrs kgaut_article_tags` remettre à zero une migration plantée