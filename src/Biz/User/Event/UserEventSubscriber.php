<?php
namespace Biz\User\Event;

use Codeages\Biz\Framework\Context\BizAware;
use Codeages\Biz\Framework\Event\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber extends BizAware implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'user.created' => 'onUserCreated',
            'user.deleted' => 'onUserDeleted',
        );
    }

    public function onUserCreated(Event $event)
    {
        $user = $event->getSubject();
        
    }

    public function onUserDeleted(Event $event)
    {
        $user = $event->getSubject();

    }
}