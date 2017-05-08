<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cake\Utility\Hash;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Ttskch\Silex\Provider\PaginationServiceProvider;

$app = new Application();
$app['debug'] = true;

$app->register(new TwigServiceProvider());
$app['twig.path'] = array(__DIR__);
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), [
    'locale_fallbacks' => ['en'],
]);
$app->register(new PaginationServiceProvider());

// just for demo.
$app['knp_paginator.path'] = __DIR__ . '/../vendor/knplabs/knp-paginator-bundle';

// sample configuration.
$app['knp_paginator.options'] = array(
    'template' => array(
        'pagination' => '@ttskch_silex_pagination/pagination-bootstrap3.html.twig',
        'filtration' => '@ttskch_silex_pagination/filtration-bootstrap3.html.twig',
    ),
);

// sample database.
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/sample.db',
    ),
));

$app->get('/', function (Application $app, Request $request) {

    // sample data.
    $array = array();
    for ($i = 1; $i <= 100; $i++) {
        $array[] = array(
            'id' => $i,
            'value' => sha1($i),
        );
    }

    $page = $request->get('page', 1);
    $limit = $request->get('limit', 10);
    $sort = $request->get('sort', 'id');
    $direction = $request->get('direction', 'asc');
    $filterField = $request->get('filterField');
    $filterValue = $request->get('filterValue');

    // filter and sort array with Cake\Utility\Hash.
    $array = Hash::extract($array, "{n}[{$filterField}=/{$filterValue}/]"); // partial match.
//    $array = Hash::extract($array, "{n}[{$filterField}=/^{$filterValue}$/]"); // perfect match.
    $array = Hash::sort($array, "{n}.{$sort}", $direction);

    $pagination = $app['knp_paginator']->paginate($array, $page, $limit);

    return $app['twig']->render('index.html.twig', array(
        'pagination' => $pagination,
    ));
})
;

$app->get('/dbal', function (Application $app, Request $request) {

    $page = $request->get('page', 1);
    $limit = $request->get('limit', 10);
    $sort = $request->get('sort');
    $direction = $request->get('direction', 'asc') === 'asc' ? 'asc' : 'desc';
    $filterField = $request->get('filterField');
    $filterValue = $request->get('filterValue');

    $qb = $app['db']->createQueryBuilder()
        ->select('s.*')
        ->from('sample', 's')
        ->where("{$app['db']->quoteIdentifier($filterField)} like {$app['db']->quote('%' . $filterValue . '%')}")
        ->orderBy($app['db']->quoteIdentifier($sort), $direction)
    ;

    $pagination = $app['knp_paginator']->paginate($qb, $page, $limit);

    return $app['twig']->render('index.html.twig', array(
        'pagination' => $pagination,
    ));
})
;

$app->run();
