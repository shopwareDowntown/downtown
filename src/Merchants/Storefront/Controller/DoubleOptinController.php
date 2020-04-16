<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Storefront\Controller;

use Shopware\Core\Checkout\Customer\Exception\CustomerAlreadyConfirmedException;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundByHashException;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountRegistrationService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class DoubleOptinController extends StorefrontController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    public function __construct(
        EntityRepositoryInterface $merchantRepository
    ) {
        $this->merchantRepository = $merchantRepository;
    }

    /**
     * @Route("/merchant/registration/confirm", name="storefront.merchant.register.mail", methods={"GET"})
     */
    public function confirmRegistration(SalesChannelContext $context, QueryDataBag $queryDataBag): Response
    {
        if (!$queryDataBag->get('hash')) {
            return new RedirectResponse(getenv('MERCHANT_PORTAL'));
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('activationCode', $queryDataBag->get('hash')));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, $context->getContext())->first();

        if (!$merchant) {
            return new RedirectResponse(getenv('MERCHANT_PORTAL'));
        }

        $this->merchantRepository->update([
            [
                'id' => $merchant->getId(),
                'activationCode' => null
            ]
        ], $context->getContext());

        return new RedirectResponse(getenv('MERCHANT_PORTAL') . '?merchantRegistrationCompleted=1');
    }
}
