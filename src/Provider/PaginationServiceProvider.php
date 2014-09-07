<?php
namespace Quartet\Silex\Provider;

use Quartet\Silex\Service\PaginationService;
use Quartet\Silex\Twig\Extension\PaginationExtension;
use Silex\Application;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\ServiceProviderInterface;

class PaginationServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app['pagination'] = $app->share(function ($app) {
            if (!isset($app['twig'])) {
                $app->register(new TwigServiceProvider());
            }
            if (!isset($app['url_generator'])) {
                $app->register(new UrlGeneratorServiceProvider());
            }
            if (!isset($app['translator'])) {
                $app->register(new TranslationServiceProvider());
            }

            $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
                $twig->addExtension(new PaginationExtension($app));
                return $twig;
            }));
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../Views', 'quartet_silex_pagination');

            return new PaginationService($app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
