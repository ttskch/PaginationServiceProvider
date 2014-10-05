<?php
namespace Quartet\Silex\Provider;

use Silex\Application;

class PaginationServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register(new PaginationServiceProvider());
    }

    // todo
    public function test()
    {
        $this->assertTrue(true);
    }
}
