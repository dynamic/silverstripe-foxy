# SilverStripe Foxy

Foxy.io integration for SilverStripe websites.

![CI](https://github.com/dynamic/silverstripe-foxy/workflows/CI/badge.svg)

[![Latest Stable Version](https://poser.pugx.org/dynamic/silverstripe-foxy/v/stable)](https://packagist.org/packages/dynamic/silverstripe-foxy)
[![Total Downloads](https://poser.pugx.org/dynamic/silverstripe-foxy/downloads)](https://packagist.org/packages/dynamic/silverstripe-foxy)
[![License](https://poser.pugx.org/dynamic/silverstripe-foxy/license)](https://packagist.org/packages/dynamic/silverstripe-foxy)


## Requirements

* SilverStripe ^5.0
* PHP ^8.1

## Installation

```
composer require dynamic/silverstripe-foxy
```

## License

See [License](license.md)

## Environment Configuration

FoxyStripe uses the `FOXY_CART_URL` and `FOXY_STORE_SECRET` environment variables. These can be set in your `.env` file:

```dotenv
FOXY_CART_URL="your-cart-url"
FOXY_STORE_SECRET="your-store-secret"
```

## Example configuration

Add the following extensions and configuration options to `foxy.yml`. Note the use of backticks to reference environment variables:

```yaml

PageController:
  extensions:
    - Dynamic\Foxy\Extension\PurchasableExtension

Dynamic\Products\Page\Product:
  extensions:
    - Dynamic\Foxy\Extension\Purchasable

Dynamic\Foxy\Model\FoxyHelper:
  cart_url: '`FOXY_CART_URL`'      # mapped to FOXY_CART_URL env var
  secret: '`FOXY_STORE_SECRET`'    # mapped to FOXY_STORE_SECRET env var
  custom_ssl: 0     # (optional) enable custom ssl setting from Foxy store advanced settings
  max_quantity: 10  # maximum number of the same product that can be added to the cart
  product_classes:
    - Dynamic\Products\Page\Product
  include_product_subclasses: 1   # (optional) include subclasses of product_classes in queries
```

Create a DataExtension `ProductOptionDataExtension`:
```php
<?

namespace {

    use Dynamic\Products\Page\Product;
    use SilverStripe\ORM\DataExtension;

    class ProductOptionDataExtension extends DataExtension
    {
        private static $belongs_many_many = [
            'Products' => Product::class,
        ];
    }
}
```

And add to `foxy.yml`:
```yaml
Dynamic\Foxy\Model\ProductOption:
  extensions:
    - ProductOptionDataExtension
```

## Product Variation Configuration

Product variations are similar to the old Product Options, however they have been reworked to use the has many relation. This allows a more robust offering for product variations such as images per variation as well as inventory control (currently roadmapped).

You will need to implement a DataExtension for the new relation type to work:

**VariationDataExtension.php**

```php
<?php

namespace {

    use Dynamic\Products\Page\Product;
    use SilverStripe\ORM\DataExtension;

    /**
     * Class VariationDataExtension
     */
    class VariationDataExtension extends DataExtension
    {
        /**
         * @var string[]
         */
        private static $has_one = [
            'Product' => Product::class,
        ];
    }
}
```

## Product option configuration
Product options can be set to trim whitespace off code modifications.
By default it will only trim spaces after the code and remove duplicate spaces.
Setting `trimAllWhitespace` to true will trim all excess whitespace.

```yaml
Dynamic\Foxy\Model\ProductOption:
  trimAllWhitespace: true
```

## Templates

To include the AddToCartForm on your page/object, use `<% include AddToCartForm %>`

## Maintainers
 *  [Dynamic](http://www.dynamicagency.com) (<dev@dynamicagency.com>)

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.
