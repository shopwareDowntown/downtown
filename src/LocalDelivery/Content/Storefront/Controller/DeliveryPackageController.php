<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\Content\Storefront\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class DeliveryPackageController extends StorefrontController
{
    /**
     * @Route(name="storefront.delivery-packages", path="/delivery-packages")
     */
    public function packages(Request $request): Response
    {
        $packages = [
            [
                'status' => 'ok cool',
                'zipCode' => '23333',
                'city' => 'hamburg',
                'street' => 'abc 123',
                'content' => 'gunther'
            ],
            [
                'status' => 'ok nich so cool',
                'zipCode' => '69420',
                'city' => 'kekistan',
                'street' => 'moin joachim 14',
                'content' => 'Frühlingsrollen'
            ]
        ];

        $html = $this->renderStorefront('@LocalDelivery/storefront/page/packages/index.html.twig', [
            'packages' => $packages
        ]);
        return new Response($html);
    }

}
