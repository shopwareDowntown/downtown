<?php declare(strict_types=1);

namespace Swag\Security\Subscriber;

use Swag\Security\Components\State;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AdminSecurityFixesProvider
{
    /**
     * @var State
     */
    private $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public function __invoke(ResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');

        if ($route !== 'api.info.config') {
            return;
        }

        $context = json_decode($event->getResponse()->getContent(), true);
        $context['swagSecurity'] = array_map(function ($state) {
            return $state::getTicket();
        }, $this->state->getActiveFixes());

        $event->setResponse(new JsonResponse($context));
    }
}
