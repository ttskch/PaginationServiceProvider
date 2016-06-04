<?php
namespace Tch\Silex\Provider;

use Silex\Application;

class PaginationServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function test_register()
    {
        $app = new Application();

        $provider = new PaginationServiceProvider();
        $provider->register($app);

        // just for test.
        $app['knp_paginator.path'] = __DIR__ . '/../../vendor/knplabs/knp-paginator-bundle';

        // providers are registered.
        $this->assertNotNull($app['twig']);
        $this->assertNotNull($app['url_generator']);
        $this->assertNotNull($app['translator']);

        // TwigExtension is added.
        $isExtensionRegistered = false;
        $extensions = $app['twig']->getExtensions();
        foreach (array_values($extensions) as $extension) {
            if (get_class($extension) === 'Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension') {}
            $isExtensionRegistered = true;
        }
        $this->assertTrue($isExtensionRegistered);

        // can fix options.
        $this->assertEmpty($app['knp_paginator.options']);
        $app['knp_paginator.options_fixer'];
        $this->assertNotEmpty($app['knp_paginator.options']);

        // paginator is registered.
        $this->assertInstanceOf('Knp\Component\Pager\Paginator', $app['knp_paginator']);

        // subscribers are registered.
        $this->assertInstanceOf('Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber', $app['knp_paginator.pagination_subscriber']);
        $this->assertInstanceOf('Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber', $app['knp_paginator.sortable_subscriber']);
        $this->assertInstanceOf('Knp\Component\Pager\Event\Subscriber\Filtration\FiltrationSubscriber', $app['knp_paginator.filtration_subscriber']);
        $this->assertInstanceOf('Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber', $app['knp_paginator.sliding_pagination_subscriber']);
    }
}
