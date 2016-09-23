<?php
namespace Ttskch\Silex;

use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Ttskch\Silex\Provider\PaginationServiceProvider;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $data = array();

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new TwigServiceProvider());
        $this->app->register(new LocaleServiceProvider());
        $this->app->register(new TranslationServiceProvider(), [
            'locale_fallbacks' => ['en'],
        ]);

        $this->app->register(new PaginationServiceProvider());
        $this->app['knp_paginator.path'] = __DIR__ . '/../vendor/knplabs/knp-paginator-bundle';

        for ($i = 0; $i < 100; $i++) {
            $this->data[] = array(
                'value' => $i,
            );
        }

        $this->app->handle(Request::create('/'));
    }

    public function test_paginate()
    {
        $pagination = $this->app['knp_paginator']->paginate($this->data);

        $this->assertInstanceOf('Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination', $pagination);
    }

    public function test_page()
    {
        $pagination = $this->app['knp_paginator']->paginate($this->data, 3);

        $this->assertEquals(3, $pagination->getCurrentPageNumber());
    }

    public function test_limit()
    {
        $pagination = $this->app['knp_paginator']->paginate($this->data, 1, 15);

        $this->assertCount(15, $pagination);
    }
}
