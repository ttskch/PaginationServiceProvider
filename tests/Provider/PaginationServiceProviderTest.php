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

    /**
     * @large
     */
    public function test()
    {
        /** @var \Quartet\Silex\Service\PaginationService $service */
        $paginator = $this->app['pagination'];

        // sample data.
        $sampleData = array();
        for ($i = 1; $i <= 100; $i++) {
            $sampleData[] = array(
                'id' => $i,
                'hash' => sha1($i),
            );
        }

        // sample routing.
        $this->app->match('/test', function () {})->bind('test');

        $paginator->paginate($sampleData);
        $view = $paginator->renderPagination('test');

        $this->assertNotEmpty($view);
    }
}
