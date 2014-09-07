<?php
namespace Quartet\Silex\Service;

use Knp\Component\Pager\Paginator;
use Quartet\Silex\Exception\LogicException;
use Silex\Application;

class PaginationService
{
    private $app;
    private $pagination;
    private $sort;
    private $direction;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function paginate($target, $page = 1, $limit = 10, array $options = array())
    {
        // if target is an array, it's must be multi-dimensional.
        if (is_array($target) && !is_array(reset($target))) {
            throw new LogicException('target array is must be multi-dimensional.');
        }

        // set sort from request query.
        if (!isset($options['sortFieldParameterName'])) {
            $options['sortFieldParameterName'] = 'sort';
        }
        if (!isset($options['sortDirectionParameterName'])) {
            $options['sortDirectionParameterName'] = 'direction';
        }
        if (!isset($_GET[$options['sortFieldParameterName']])) {
            $keys = array_keys(reset($target));
            $_GET[$options['sortFieldParameterName']] = reset($keys);
        }
        if (!isset($_GET[$options['sortDirectionParameterName']])) {
            $_GET[$options['sortDirectionParameterName']] = 'asc';
        }
        $this->sort = $_GET[$options['sortFieldParameterName']];
        $this->direction = $_GET[$options['sortDirectionParameterName']];

        // sort target if it's an array.
        if (is_array($target)) {
            $columns = array();
            foreach ($target as $index => $row) {
                foreach ($row as $key => $value) {
                    $columns[$key][$index] = $value;
                }
            }
            array_multisort($columns[$this->sort], $this->direction === 'asc' ? SORT_ASC : SORT_DESC, SORT_NATURAL, $target);
        }

        $paginator = new Paginator();
        $pagination = $paginator->paginate($target, $page, $limit, array_merge($options));

        $this->pagination = $pagination;
        return $pagination;
    }

    public function setPageRange($pageRange)
    {
        if (!is_null($this->pagination)) {
            $this->pagination->setPageRange($pageRange);
        }
    }

    public function renderPagination($route)
    {
        if (is_null($this->pagination)) {
            return 'do paginate first.';
        }

        return $this->app['twig']->render('@quartet_silex_pagination/pagination.html.twig', array(
            'data' => $this->pagination->getPaginationData(),
            'route' => $route,
        ));
    }

    public function renderSortable()
    {
    }
}
