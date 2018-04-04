Installation:
Step 1. Add module to the composer.json
"dawhoo/configurableproducts": "dev-master"
Example:

"require": {
    "magento/product-community-edition": "2.2.0",
    "composer/composer": "@alpha",
    "mjankiewicz/configurableproduct": "dev-master"
}
Step 2. Update composer
composer update

Step 3. Enable the module:
php bin/magento module:enable Dawhoo_ConfigurableProducts

Step 4 Upgrade the Magento application
php bin/magento setup:upgrade

Step 5. Generate DI configuration
php bin/magento setup:di:compile

Step 6. Clear cache
php bin/magento cache:flush
