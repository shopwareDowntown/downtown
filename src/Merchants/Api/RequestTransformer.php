<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Api;

use Shopware\Core\Framework\Routing\RequestTransformerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestTransformer implements RequestTransformerInterface
{
    /**
     * @var RequestTransformerInterface
     */
    private $decorated;

    public function __construct(RequestTransformerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function transform(Request $request): Request
    {
        if (strpos($request->getPathInfo(), '/merchant-api') === 0) {
            return $request;
        }

        return $this->decorated->transform($request);
    }

    public function extractInheritableAttributes(Request $sourceRequest): array
    {
        return $this->decorated->extractInheritableAttributes($sourceRequest);
    }
}
