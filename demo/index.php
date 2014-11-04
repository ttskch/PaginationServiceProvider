<?php
use Quartet\Silex\Provider\PaginationServiceProvider;
use Quartet\Silex\Service\ArrayHandler;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = array(__DIR__);
$app->register(new PaginationServiceProvider());

// just for demo.
$app['knp_paginator.path'] = __DIR__ . '/../vendor/knplabs/knp-paginator-bundle';

// sample configuration.
$app['knp_paginator.options'] = array(
    'template' => array(
        'pagination' => '@quartet_silex_pagination/pagination-bootstrap3.html.twig',
        'filtration' => '@quartet_silex_pagination/filtration-bootstrap3.html.twig',
    ),
);

$app->get('/', function (Request $request) use ($app) {

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
    $direction = $request->get('direction') === 'desc' ? ArrayHandler::DESC : ArrayHandler::ASC;
    $filterField = $request->get('filterField');
    $filterValue = $request->get('filterValue');

    // sort and filter array.
    $array = $app['knp_paginator.array_handler']->filter($array, $filterField, $filterValue);
    $array = $app['knp_paginator.array_handler']->sort($array, $sort, $direction);

    $pagination = $app['knp_paginator']->paginate($array, $page, $limit);

    return $app['twig']->render('index.html.twig', array(
        'pagination' => $pagination,
    ));
})
;

$app->get('/raw', function () use ($app) {
    $code = file_get_contents(__DIR__ . '/index.html.twig');
    return '<pre>' . htmlspecialchars($code) . '</pre>';
})
;

$app->run();
