<?php
namespace Quartet\Silex\Twig\Extension;

use Silex\Application;

class PaginationExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pagination_render', array($this, 'render')),
            new \Twig_SimpleFunction('pagination_sortable', array($this, 'sortable')),
        );
    }

    public function render()
    {
    }

    public function sortable()
    {
    }

    public function getName()
    {
        return 'quartet_silex_pagination_extension';
    }
}
