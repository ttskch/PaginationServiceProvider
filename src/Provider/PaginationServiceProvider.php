<?php
namespace Quartet\Silex\Provider;

use Quartet\Silex\Service\PaginationService;
use Quartet\Silex\Subscriber\PaginationSubscriber;
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
        if (!isset($app['twig'])) {
            $app->register(new TwigServiceProvider());
        }
        if (!isset($app['url_generator'])) {
            $app->register(new UrlGeneratorServiceProvider());
        }
        if (!isset($app['translator'])) {
            $app->register(new TranslationServiceProvider());
        }

        $app['pagination.options'] = array();

        $app['pagination'] = $app->share(function ($app) {
            $app['pagination.options'] = array_replace(
                array(
                    'limits' => array(25, 50, 100, 200, 500),
                ), $app['pagination.options']
            );

            $app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
                $twig->addExtension(new PaginationExtension($app));
                return $twig;
            }));
            $loader = new \Twig_Loader_Filesystem();
            $loader->addPath(__DIR__ . '/../Views', 'quartet_silex_pagination');
            $app['twig.loader']->addLoader($loader);

            return new PaginationService($app);
        });

        $app['pagination.subscriber'] = new PaginationSubscriber();
        $app['dispatcher']->addSubscriber($app['pagination.subscriber']);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
