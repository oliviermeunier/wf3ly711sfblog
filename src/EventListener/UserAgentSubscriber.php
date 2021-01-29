<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UserAgentSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
    //            'kernel.request' => 'onKernelRequest'
            RequestEvent::class => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        // If this is a sub-request, we return immediately
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $request->attributes->set('_isMac', $this->isMac($request));

//        $request = $event->getRequest();
//        $userAgent = $request->headers->get('User-Agent');
//
//        $isMac = str_contains($userAgent, 'Mac');
//        $request->attributes->set('isMac', $isMac);

//        $this->logger->info(sprintf('The User-Agent is "%s"', $userAgent));

//        $event->setResponse(new Response(
//             'Ah, ah, ah: you didn\'t say the magic word'
//        ));

//        $request->attributes->set('_controller', function() {
//           return new Response('I just took over the controller ah ahrgf');
//        });
    }

    private function isMac(Request $request): bool
    {
        if ($request->query->has('mac')) {
            return $request->query->getBoolean('mac');
        }

        $userAgent = $request->headers->get('User-Agent');

        return str_contains($userAgent, 'Mac');
    }
}