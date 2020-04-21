<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class PaymentMethodsController
{
    /**
     * @var SalesChannelRepositoryInterface
     */
    private $repoPaymentMethods;


    public function __construct(SalesChannelRepositoryInterface $repoPaymentMethods)
    {
        $this->repoPaymentMethods = $repoPaymentMethods;
    }

    /**
     * @OA\Get(
     *      path="/paymentmethods",
     *      description="List all available payment methods",
     *      operationId="loadAllPaymentMethods",
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(
     *              ref="#/definitions/PaymentMethodResponse"
     *          )
     *     )
     * )
     * @Route(name="merchant-api.paymentmethods", path="/merchant-api/v{version}/paymentmethods", methods={"GET"})
     */
    public function load(SalesChannelContext $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('salesChannels.id', $context->getSalesChannel()->getId()));
        $criteria->addAssociation('salesChannels');

        $result = $this->repoPaymentMethods->search($criteria, $context);

        return new JsonResponse([
            'total' => $result->getTotal(),
            'data' => $result->getEntities()
        ]);
    }
}
