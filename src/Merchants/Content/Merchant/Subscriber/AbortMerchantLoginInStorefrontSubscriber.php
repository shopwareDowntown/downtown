<?php

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\HttpFoundation\RequestStack;

class AbortMerchantLoginInStorefrontSubscriber
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityRepositoryInterface $merchantRepository, RequestStack $requestStack)
    {
        $this->merchantRepository = $merchantRepository;
        $this->requestStack = $requestStack;
    }

    public function __invoke(CustomerLoginEvent $loginEvent)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $loginEvent->getCustomer()->getId()));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, $loginEvent->getContext())->first();

        if ($merchant && $this->isStorefrontLogin()) {
            throw new CustomerNotFoundException($loginEvent->getCustomer()->getEmail());
        }
    }

    private function isStorefrontLogin(): bool
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            return false;
        }

        return $request->attributes->get('_route') === 'frontend.account.login';
    }
}
