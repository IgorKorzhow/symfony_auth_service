<?php

namespace App\EventSubscriber\UserRegistered;

use App\Enum\NotificationTypeEnum;
use App\Event\UserRegisteredEvent;
use App\Message\NotificationMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener]
readonly class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $bus
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        $message = new NotificationMessage(
            type: NotificationTypeEnum::EMAIL_NOTIFICATION,
            userEmail: $user->getEmail(),
        );

        $this->bus->dispatch($message);
    }
}
