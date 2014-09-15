<?php
namespace Quartet\Silex\Service;

use Knp\Component\Pager\Paginator;
use Quartet\Silex\Exception\RuntimeException;
use Quartet\Silex\Pagination\SlidingPagination;
use Silex\Application;

class PaginationService
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function paginate($target)
    {
        // todo: only array is supported for now...
        if (!is_array($target)) {
            throw new RuntimeException('only array is supported for target.');
        }

        // if target is an array, it's must be multi-dimensional.
        if (is_array($target) && !is_array(reset($target))) {
            throw new RuntimeExceptionException('target array is must be multi-dimensional.');
        }

        $page = $this->app['request']->get('page') ?: 1;
        $limit = $this->app['request']->get('limit') ?: $this->app['pagination.options']['limits'][0];

        $paginator = new Paginator();
        $paginator->subscribe($this->app['pagination.subscriber']);
        $pagination = $paginator->paginate($target, $page, $limit);

        return $pagination;
    }

    public function renderPagination(SlidingPagination $pagination)
    {
        return $this->app['twig']->render('@quartet_silex_pagination/pagination-bootstrap3.html.twig', array(
            'data' => $pagination->getPaginationData(),
            'route' => $pagination->getRoute(),
            'sort' => $pagination->getSort(),
            'direction' => $pagination->getDirection(),
            'options' => $this->app['pagination.options'],
        ));
    }

    public function renderSortable(SlidingPagination $pagination, $key)
    {
        $toggle = function ($direction) {
            return $direction === 'asc' ? 'desc' : 'asc';
        };

        $limit = $pagination->getLimit();
        $sort = $key;
        $isSorted = ($pagination->getSort() === $key);
        $direction = $isSorted ? $toggle($pagination->getDirection()) : 'asc';

        $url = $this->app['url_generator']->generate($pagination->getRoute(), compact('limit', 'sort', 'direction'));

        return $this->app['twig']->render('@quartet_silex_pagination/sortable.html.twig', array(
            'options' => array(
                'href' => $url,
                'class' => ($isSorted ? "sorted sorted-{$toggle($direction)}" : ''),
            ),
            'title' => $key,
        ));
    }
}
