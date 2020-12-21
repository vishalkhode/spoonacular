<p align="center">
  <img src="https://drupalize.me/sites/default/files/blog_post_images/9.0_blue_rgb.png">
</p>

## Introduction
Spoonacular module is created to migrate content from [Spoonacular](https://spoonacular.com/) in drupal 9. It makes use of drupal core Migrate API and [Migrate Plus](https://www.drupal.org/project/migrate_plus) contributed module.
Module provides following features:
* New Recipe content type with following fields:
    * Recipe Name (Required plain text field).
    * Cooking instructions (Textarea field for adding cooking instructions for the recipe).
    * Image (Recipe Image, which is required Media field).
    * Categories (Term reference field refering to Cuisine Vocabulary).
    * Ingredient (Paragraph field for adding ingredients having following sub fields):
       * Ingredient (Term reference field refering to Ingredients Vocabulary)
       * Title (Text field)
       * Measurement Field (Custom measurement field for adding ingredient quantity, unit etc.)
    * Video (An optional text field of type Embed video).
* Adds two vocabularies:
    * Cuisine
    * Ingredients
* Recipe Listing page `/recipes` which provides list of all recipes which includes pagination & category filter at top.
* Module configuration page `/admin/config/system/spoonacular` providing following configurations:
    * Use Mock API checkbox: When checked, content will be migrated from Mock API (instead of Spoonacular API).
    * Migrate recipes during CRON run ? When checked, content will be migrated automatically whenver drupal CRON runs. If you do not wish to migrate content during CRON, you can run following drush command or can add it in your scheduled jobs: `drush migrate-recipes` or `drush mim --group=recipe`.


## Requirements
Download latest Drupal 9 project. You can run below command to download:

```sh
composer create-project drupal/recommended-project recipe-online
```

After Drupal is downloaded, download all required contributed modules:

```sh
composer require "drupal/migrate_plus"
composer require "drupal/migrate_tools"
composer require "drupal/paragraphs"
composer require "drupal/video_embed_field"
```

Download this spoonacular module and place it inside `web/modules/custom` directory.

Install the drupal 9 now with `Standard` profile.

* Make sure public files directory (i.e. `sites/default/files`) is writable by server. If not, please provide write permission to it.
* Make sure to set private files directory path in `settings.php` and verify if it's writable by server. Ex:
```sh
$settings['file_private_path'] = DRUPAL_ROOT . '/../files-private';
```
* After setting private files directory, clear Drupal Caches. (This is required).
* Create a free account in [Spoonacular](https://spoonacular.com/food-api) and get the API Key. Api Key can be found under [Profile](https://spoonacular.com/food-api/console#Profile) page.


## Usage
* Navigate to module install page and install Spoonacular module. Module should install successfully without any error.
* Navigate to module configuration page (i.e. `/admin/config/system/spoonacular`) and you'll find different options.
* There's a checkbox `Migrate recipes during CRON run ?`. When checked, content will be migrated automatically during cron runs. 
* To run cron manually, navigate to cron page (i.e `/admin/config/system/cron`) and click on Run Cron button. 

### Once cron is completed, you will see 10 recipes migrated in drupal.

## Testing

Configure PHPUnit in local. You can follow steps as mentioned in [drupal.org](https://www.drupal.org/docs/automated-testing/phpunit-in-drupal/running-phpunit-tests#s-configure-phpunit).


Following modules are required for automation testing:
```sh
composer require phpspec/prophecy-phpunit:^2 --dev
composer require behat/mink-goutte-driver --dev
composer require symfony/phpunit-bridge --dev
composer require "cweagans/composer-patches:^1.7"
```

There's a core [issue](https://www.drupal.org/node/3186443) which gives `Call to undefined method error` when running tests with latest PHPUnit 9.5. So, add below patch in `composer.json` to fix it:
```sh
"extra": {
        "enable-patching": true,
        "patches": {
          "drupal/core": {
            "Call to undefined method ::getAnnotations()": "https://www.drupal.org/files/issues/2020-12-04/3186443-1.patch"
          }
        },
```

Run below phpunit tests command and you should see all tests are getting executed successfully.
```sh
./vendor/bin/phpunit -c web/core/phpunit.xml web/modules/custom/spoonacular/
```