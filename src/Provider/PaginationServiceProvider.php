<?php

namespace Ttskch\Silex\Provider;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber;
use Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension;
use Knp\Component\Pager\Event\Subscriber\Filtration\FiltrationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber;
use Knp\Component\Pager\Paginator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Silex\Api\BootableProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginationServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        if (!isset($app['twig'])) {
            throw new \LogicException('You must register the TwigServiceProvider to use the PaginationServiceProvider.');
        }
        if (!isset($app['translator'])) {
            throw new \LogicException('You must register the TranslationServiceProvider to use the PaginationServiceProvider.');
        }

        // add twig extension.
        $app['twig'] = $app->extend('twig', function ($twig) use ($app) {
            $processor = new Processor($app['url_generator'], $app['translator']);
            $twig->addExtension(new PaginationExtension($processor));

            return $twig;
        });
        
        if(!isset($app['knp_paginator.options'])){
            $app['knp_paginator.options'] = [];
        }

        $app['knp_paginator.path'] = __DIR__ . '/../../../../knplabs/knp-paginator-bundle';
        $app['knp_paginator.limits'] = [10, 25, 50, 100, 200, 500];

        // options fixer.
        $app['knp_paginator.options_fixer'] = function (Container $app) {
            $app['knp_paginator.options'] = array_replace_recursive(
                [
                    'default_options' => [
                        'sort_field_name' => 'sort',
                        'sort_direction_name' => 'direction',
                        'filter_field_name' => 'filterField',
                        'filter_value_name' => 'filterValue',
                        'page_name' => 'page',
                        'distinct' => true,
                    ],
                    'template' => [
                        'pagination' => '@knp_paginator_bundle/sliding.html.twig',
                        'filtration' => '@knp_paginator_bundle/filtration.html.twig',
                        'sortable' => '@knp_paginator_bundle/sortable_link.html.twig',
                    ],
                    'page_range' => 5,
                ], $app['knp_paginator.options']
            );
        };

        // paginator creator.
        $app['knp_paginator'] = function (Container $app) {
            // add twig template paths.
            $loader = new \Twig_Loader_Filesystem();
            $loader->addPath(__DIR__ . '/../Views', 'ttskch_silex_pagination');
            $loader->addPath(rtrim($app['knp_paginator.path'], '/') . '/Resources/views/Pagination', 'knp_paginator_bundle');
            $app['twig.loader']->addLoader($loader);

            // fix options.
            $app['knp_paginator.options_fixer'];

            // create paginator.
            $paginator = new Paginator($app['dispatcher']);
            $paginator->setDefaultPaginatorOptions([
                'pageParameterName' => $app['knp_paginator.options']['default_options']['page_name'],
                'sortFieldParameterName' => $app['knp_paginator.options']['default_options']['sort_field_name'],
                'sortDirectionParameterName' => $app['knp_paginator.options']['default_options']['sort_direction_name'],
                'filterFieldParameterName' => $app['knp_paginator.options']['default_options']['filter_field_name'],
                'filterValueParameterName' => $app['knp_paginator.options']['default_options']['filter_value_name'],
                'distinct' => $app['knp_paginator.options']['default_options']['distinct'],
            ]);

            return $paginator;
        };

        // event subscribers.
        $app['knp_paginator.pagination_subscriber'] = function () {
            return new PaginationSubscriber();
        };

        $app['knp_paginator.sortable_subscriber'] = function () {
            return new SortableSubscriber();
        };

        $app['knp_paginator.filtration_subscriber'] = function () {
            return new FiltrationSubscriber();
        };

        $app['knp_paginator.sliding_pagination_subscriber'] = function (Container $app) {
            // fix options.
            $app['knp_paginator.options_fixer'];

            return new SlidingPaginationSubscriber(array(
                'defaultPaginationTemplate' => $app['knp_paginator.options']['template']['pagination'],
                'defaultSortableTemplate' => $app['knp_paginator.options']['template']['sortable'],
                'defaultFiltrationTemplate' => $app['knp_paginator.options']['template']['filtration'],
                'defaultPageRange' => $app['knp_paginator.options']['page_range'],
            ));
        };
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['knp_paginator.pagination_subscriber']);
        $app['dispatcher']->addSubscriber($app['knp_paginator.sortable_subscriber']);
        $app['dispatcher']->addSubscriber($app['knp_paginator.filtration_subscriber']);
        $app['dispatcher']->addSubscriber($app['knp_paginator.sliding_pagination_subscriber']);
        $app['dispatcher']->addListener(KernelEvents::REQUEST, [$app['knp_paginator.sliding_pagination_subscriber'], 'onKernelRequest']);
    }
}
