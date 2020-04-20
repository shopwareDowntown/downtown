<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Api;

use OpenApi\Annotations\Components;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\SecurityScheme;
use OpenApi\Annotations\Server;
use Shopware\Core\Framework\Api\ApiDefinition\Generator\OpenApi\DeactivateValidationAnalysis;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function OpenApi\scan;
use const OpenApi\Annotations\UNDEFINED;

/**
 * @RouteScope(scopes={"organization-api", "merchant-api"})
 */
class InfoController extends AbstractController
{
    private const OPERATION_KEYS = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
    ];

    private const API_INFO = [
        'merchant' => [
            'url' => '/merchant-api/v1',
            'route' => 'merchant-api.info.openapi3',
            'tag' => 'Merchant',
            'scanDirs' => [
                __DIR__ . '/../../Portal',
                __DIR__ . '/../../Merchants',
            ]
        ],
        'organization' => [
            'url' => '/merchant-api/v1',
            'route' => 'organization-api.info.openapi3',
            'tag' => 'Organization',
            'scanDirs' => [
                __DIR__ . '/../../Portal',
                __DIR__ . '/../../Organization',
            ]
        ]
    ];

    /**
     * @Route("/organization-api/v{version}/_info/swagger.html", defaults={"auth_required"=false, "type"="organization"}, name="organization-api.info.swagger", methods={"GET"})
     * @Route("/merchant-api/v{version}/_info/swagger.html", defaults={"auth_required"=false, "type"="merchant"}, name="merchant-api.info.swagger", methods={"GET"})
     */
    public function infoHtml(Request $request, int $version): Response
    {
        $info = self::API_INFO[$request->attributes->get('type')];
        return $this->render('@Framework/swagger.html.twig', ['schemaUrl' => $info['route'], 'apiVersion' => '1']);
    }

    /**
     * @Route("/organization-api/v{version}/_info/openapi3.json", defaults={"auth_required"=false, "type"="organization"}, name="organization-api.info.openapi3", methods={"GET"})
     * @Route("/merchant-api/v{version}/_info/openapi3.json", defaults={"auth_required"=false, "type"="merchant"}, name="merchant-api.info.openapi3", methods={"GET"})
     */
    public function info(Request $request): JsonResponse
    {
        $info = self::API_INFO[$request->attributes->get('type')];
        $openApi = scan($info['scanDirs'], ['analysis' => new DeactivateValidationAnalysis()]);

        $allUndefined = true;
        $calculatedPaths = [];
        foreach ($openApi->paths as $pathItem) {
            foreach (self::OPERATION_KEYS as $key) {
                /** @var Operation $operation */
                $operation = $pathItem->$key;
                if ($operation instanceof Operation && !in_array($info['tag'], $operation->tags, true)) {
                    $pathItem->$key = UNDEFINED;
                }
                $allUndefined = ($pathItem->$key === UNDEFINED && $allUndefined === true);
            }

            if (!$allUndefined) {
                $calculatedPaths[] = $pathItem;
            }
        }
        $openApi->paths = $calculatedPaths;

        $this->addDefaults($openApi, $info);

        $data = json_decode($openApi->toJson(), true);
        $finder = (new Finder())->in(__DIR__ . '/Schema')->name('*.json');

        foreach ($finder as $item) {
            $name = str_replace('.json', '', $item->getFilename());

            $readData = json_decode(file_get_contents($item->getPathname()), true);
            $data['definitions'][$name] = $readData;
        }

        return new JsonResponse($data);
    }

    private function addDefaults(OpenApi $openApi, array $info): void
    {
        $openApi->merge([
            new Server(['url' => rtrim(getenv('APP_URL'), '/') . $info['url']]),
        ]);

        $openApi->info = new Info([
            'title' => 'API',
            'version' => '1.0.0',
        ]);

        if (!$openApi->components instanceof Components) {
            $openApi->components = new Components([]);
        }

        $openApi->components->merge([
            'ApiKey' => new SecurityScheme([
                'securityScheme' => 'ApiKey',
                'type' => 'apiKey',
                'in' => 'header',
                'name' => PlatformRequest::HEADER_CONTEXT_TOKEN,
            ]),
        ]);
    }
}
