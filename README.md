# PaginationServiceProvider

[![Build Status](https://travis-ci.org/qckanemoto/PaginationServiceProvider.svg?branch=basic-functionalities)](https://travis-ci.org/qckanemoto/PaginationServiceProvider)
[![Latest Stable Version](https://poser.pugx.org/qckanemoto/pagination-service-provider/version.svg)](https://packagist.org/packages/qckanemoto/pagination-service-provider)
[![Total Downloads](https://poser.pugx.org/qckanemoto/pagination-service-provider/downloads.svg)](https://packagist.org/packages/qckanemoto/pagination-service-provider)

pagination service provider for the Silex microframework.

## Requirements

* PHP 5.3+
* [Bootstrap3](http://getbootstrap.com/)

## Getting Started

First add this dependency into your `composer.json`:

```json
{
    "require": {
        "qckanemoto/pagination-service-provider: "dev-master"
    }
}
```

And enable this service provider in your app:

```php
$app->register(new Quartet\Silex\Provider\PaginationServiceProvider());
```

Then you can create pagination instance and render it in view:

```php
// in your controller.

$pagination = $app['pagination']->paginate($someData);

return $app['twig']->render('index.html.twig', array(
    'pagination' => $pagination,
));
```

```html
{# index.html.twig #}

{{ pagination_render(pagination) }}

<table>
    <thead>
    <tr>
        {% for key in pagination.getKeys() %}
            <th>{{ pagination_sortable(pagination, key) }}</th>
        {% endfor %}
    </tr>
    </thead>
    <tbody>
    {% for row in pagination %}
        <tr>
            {% for item in row %}
                <td>{{ item }}</td>
            {% endfor %}
        </tr>
    {% endfor %}
    </tbody>
</table>
```

You can see demo code [here](demo).

## Additional features

### Configuring number of items per page

You can configure numbers of items per page as below:

```php
$app['pagination.options'] = array(
    'limits' => array(25, 50, 100, 200, 500),
);
```

### Translations

You can define translations for pagination as below:

```php
$app['translator.domains'] = array(
    'paginations' => array(
        'ja' => array(
            'First' => '最初',
            'Last' => '最後',
            'Previous' => '前へ',
            'Next' => '次へ',
            'Items per page' => '1ページの件数',
        ),
    ),
);
```

## Note

This service provider depends on `TwigServiceProvider`, `UrlGeneratorServiceProvider` and `TranslationServiceProvider`, but you don't have to worry about that because they will be automatically enabled via this provider.
