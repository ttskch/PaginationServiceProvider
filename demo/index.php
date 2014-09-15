<?php
use Quartet\Silex\Provider\PaginationServiceProvider;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app->register(new TwigServiceProvider());
$app['twig.path'] = array(__DIR__);
$app->register(new PaginationServiceProvider());

$app['debug'] = true;

$app->get('/', function () use ($app) {

    // sample data.
    $sampleData = array();
    for ($i = 1; $i <= 100; $i++) {
        $sampleData[] = array(
            'id' => $i,
            'value' => sha1($i),
        );
    }

    $pagination = $app['pagination']->paginate($sampleData);

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
