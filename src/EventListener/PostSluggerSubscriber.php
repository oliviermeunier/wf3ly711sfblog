<?php


namespace App\EventListener;


use App\Entity\Post;
use App\Service\PostSlugUpdater;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostSluggerSubscriber implements EventSubscriber
{
    private $postSlugUpdater;

    public function __construct(PostSlugUpdater $postSlugUpdater)
    {
        $this->postSlugUpdater = $postSlugUpdater;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();

        if (! $entity instanceof Post) {
            return;
        }

        $this->postSlugUpdater->updateSlug($entity);
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();

        if (! $entity instanceof Post) {
            return;
        }

        $this->postSlugUpdater->updateSlug($entity);
    }
}