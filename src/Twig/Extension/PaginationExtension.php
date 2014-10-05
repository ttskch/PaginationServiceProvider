<?php
namespace Quartet\Silex\Twig\Extension;

use Silex\Application;

class PaginationExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagination_render', array($this->app['pagination'], 'renderPagination'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('pagination_sortable', array($this->app['pagination'], 'renderSortable'), array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return 'quartet_silex_pagination_extension';
    }
}
