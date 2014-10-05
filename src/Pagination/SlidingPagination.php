<?php
namespace Quartet\Silex\Pagination;

use Knp\Component\Pager\Pagination\SlidingPagination as BaseSlidingPagination;

class SlidingPagination extends BaseSlidingPagination
{
    private $route;
    private $page;
    private $limit;
    private $sort;
    private $direction;

    public function __construct($route, $sort, $direction)
    {
        $this->route = $route;
        $this->page = $this->currentPageNumber;
        $this->limit = $this->numItemsPerPage;
        $this->sort = $sort;
        $this->direction = $direction;

    }

    public function getRoute()
    {
        return $this->route;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function getKeys()
    {
        if ($item = reset($this->items)) {
            return array_keys($item);
        }
        return array();
    }
}
