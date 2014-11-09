# PaginationServiceProvider

[![Build Status](https://travis-ci.org/qckanemoto/PaginationServiceProvider.svg?branch=master)](https://travis-ci.org/qckanemoto/PaginationServiceProvider)
[![Latest Stable Version](https://poser.pugx.org/qckanemoto/pagination-service-provider/version.svg)](https://packagist.org/packages/qckanemoto/pagination-service-provider)
[![Total Downloads](https://poser.pugx.org/qckanemoto/pagination-service-provider/downloads.svg)](https://packagist.org/packages/qckanemoto/pagination-service-provider)

This service provider allows you to use [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) in your Silex application.

## Requirements

* PHP 5.3+

## Getting Started

First add this dependency into your `composer.json`:

```json
{
    "require": {
        "qckanemoto/pagination-service-provider": "dev-master"
    }
}
```

And enable this service provider in your app:

```php
$app->register(new Quartet\Silex\Provider\PaginationServiceProvider());
```

If you need, you can configure default query parameter names and templates as below (almost same as [origin](https://github.com/KnpLabs/KnpPaginatorBundle#configuration-example)):

```php
$app['knp_paginator.options'] = array(
    'default_options' => array(
        'sort_field_name' => 'sort',
        'sort_direction_name' => 'direction',
        'filter_field_name' => 'filterField',
        'filter_value_name' => 'filterValue',
        'page_name' => 'page',
        'distinct' => true,
    ),
    'template' => array(
        'pagination' => '@knp_paginator_bundle/sliding.html.twig',
        'filtration' => '@knp_paginator_bundle/filtration.html.twig',
        'sortable' => '@knp_paginator_bundle/sortable_link.html.twig',
    ),
    'page_range' => 5,
);
```

Then you can create pagination instance and render it in view:

```php
// in your controller.

$pagination = $app['knp_paginator']->paginate($someData);

return $app['twig']->render('index.html.twig', array(
    'pagination' => $pagination,
));
```

```twig
{# in your twig template #}

{{ knp_pagination_render(pagination) }}
```

You can see demo code [here](demo).

## Sort or filter array

[KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) can't sort or filter array automatically via request query parameter.
But we can't use ORM normally in Silex application and often use simple two-dimensional array.
So this service provider provides a utility to sort or filter array. You can use it as below:

```php
$array = /* some two dimensional array */;

$filtered = $app['knp_paginator.array_handler']->filter($array, $filterField, $filterValue, $matchType);
$sorted = $app['knp_paginator.array_handler']->sort($filtered, $sort, $direction);

$pagination = $app['knp_paginator']->paginate($sorted); // You will get filtered and sorted pagination object.
```

## Additional features

This service provider also provides bootstrap3-based beautiful pagination and filtration templates. You can use it as below:

```php
$app['knp_paginator.options'] = array(
    'template' => array(
        'pagination' => '@quartet_silex_pagination/pagination-bootstrap3.html.twig',
        'filtration' => '@quartet_silex_pagination/filtration-bootstrap3.html.twig',
    ),
);
```

When you use the `pagination-bootstrap3.html.twig` template, you can configure the list of `Items per page` selector.

```php
$app['knp_paginator.limits'] = array(10, 25, 50, 100, 200, 500),
```

You also can define translations for some labels in the `messages` domain.

```php
$app['translator.domains'] = array(
    'messages' => array(
        'ja' => array(
            'Previous' => '前へ',
            'Next' => '次へ',
        ),
    ),
);
```

```php
$app['translator.domains'] = array(
    'messages' => array(
        'ja' => array(
            'Items per page' => '1ページの件数',
            'Filter' => '絞り込み',
        ),
    ),
);
```

## Note

This service provider depends on `TwigServiceProvider`, `UrlGeneratorServiceProvider` and `TranslationServiceProvider`, but you don't have to worry about that because they will be automatically enabled via this provider.
