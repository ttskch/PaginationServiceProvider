<?php
namespace Quartet\Silex\Subscriber;

use Knp\Component\Pager\Event\ItemsEvent;
use Knp\Component\Pager\Event\PaginationEvent;
use Quartet\Silex\Pagination\SlidingPagination;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PaginationSubscriber implements EventSubscriberInterface
{
    private $route;
    private $sort;
    private $direction;

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $request = $event->getRequest();
        $this->route = $request->attributes->get('_route');
        $this->sort = $request->get('sort');
        $this->direction = $request->get('direction');
    }

    public function items(ItemsEvent $event)
    {
        // sort target if it's an array.
        if (is_array($event->target) && count($event->target)) {
            $columns = array();
            foreach ($event->target as $index => $row) {
                foreach ($row as $key => $value) {
                    $columns[$key][$index] = $value;
                }
            }
            if (!$this->sort) {
                $keys = array_keys(reset($event->target));
                $this->sort = $keys[0];
            }
            if (!$this->direction) {
                $this->direction = 'asc';
            }
            array_multisort($columns[$this->sort], $this->direction === 'asc' ? SORT_ASC : SORT_DESC, SORT_NATURAL, $event->target);
        }
    }

    public function pagination(PaginationEvent $event)
    {

        $pagination = new SlidingPagination($this->route, $this->sort, $this->direction);
        $event->setPagination($pagination);
        $event->stopPropagation();
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request' => 'onKernelRequest',
            'knp_pager.items' => array('items', 0),
            'knp_pager.pagination' => array('pagination', 1),
        );
    }
}
