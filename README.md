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


## Les commandes drush

nous supposerons ici que l'alias défini est @kg et que le group est
`kgaut` (oui je suis très égocentrique, mais c'est mon site après tout).

`drush @kg ms` : status des migration
`drush @kg mi kgaut_article_tags` : lancer une migration
`drush @kg mi --group=kgaut` : lancer un groupe de migrations
`drush @kg migrate-reset-status kgaut_article_tags` remettre à zero une migration plantée