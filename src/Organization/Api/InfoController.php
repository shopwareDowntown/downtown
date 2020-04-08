<?php declare(strict_types=1);

namespace Shopware\Production\Organization\Api;

use OpenApi\Annotations\Components;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\SecurityScheme;
use OpenApi\Annotations\Server;
use Shopware\Core\Framework\Api\ApiDefinition\Generator\OpenApi\DeactivateValidationAnalysis;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function OpenApi\scan;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class InfoController extends AbstractController
{
    /**
     * @Route("/organization-api/v{version}/_info/swagger.html", defaults={"auth_required"=false}, name="organization-api.info.swagger", methods={"GET"})
     */
    public function infoHtml(int $version): Response
    {
        return $this->render('@Framework/swagger.html.twig', ['schemaUrl' => 'organization-api.info.openapi3', 'apiVersion' => '1']);
    }

    /**
     * @Route("/organization-api/v{version}/_info/openapi3.json", defaults={"auth_required"=false}, name="organization-api.info.openapi3", methods={"GET"})
     */
    public function info(): JsonResponse
    {
        $openApi = scan([
            dirname(__DIR__, 2),
        ], ['analysis' => new DeactivateValidationAnalysis()]);
        $this->addDefaults($openApi);

        $data = json_decode($openApi->toJson(), true);
        $finder = (new Finder())->in(__DIR__ . '/Schema')->name('*.json');

        foreach ($finder as $item) {
            $name = str_replace('.json', '', $item->getFilename());

            $readData = json_decode(file_get_contents($item->getPathname()), true);
            $data['definitions'][$name] = $readData;
        }

        return new JsonResponse($data);
    }

    private function addDefaults(OpenApi $openApi): void
    {
        $openApi->merge([
            new Server(['url' => rtrim(getenv('APP_URL'), '/') . '/organization-api/v1']),
        ]);

        $openApi->info = new Info([
            'title' => 'Organization-API',
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
