<?php

namespace App\Subscriber;

use App\Service\IPageLoader;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaginateSubscriber implements EventSubscriberInterface
{
    public function items(ItemsEvent $event): void
    {
        if (!$event->target instanceof IPageLoader) {
            return;
        }

        /** @var IPageLoader */
        $loader = $event->target;
        $itemsPerPage = $event->getLimit();
        $page = ($event->getOffset() / $itemsPerPage) + 1;
        $dto = $loader->load($page, $itemsPerPage);

        $event->items = $dto->items;
        $event->count = $dto->total;
        if (!$dto->isOk) {
            $event->setCustomPaginationParameter(IPageLoader::ERROR_PARAM, true);
        }

        $event->stopPropagation();
    }

    public static function getSubscribedEvents(): array
    {
        return ['knp_pager.items' => ['items', 1]];
    }
}
