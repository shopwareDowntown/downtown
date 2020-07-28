<?php declare(strict_types=1);

namespace Swag\Security\Api;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Adapter\Cache\CacheIdLoader;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Swag\Security\Components\State;
use Swag\Security\SwagPlatformSecurity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class SecurityController
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var EntityRepositoryInterface
     */
    private $pluginRepository;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var CacheIdLoader
     */
    private $cacheIdLoader;

    public function __construct(
        State $state,
        EntityRepositoryInterface $pluginRepository,
        string $cacheDir,
        Client $client,
        CacheIdLoader $cacheIdLoader
    ) {
        $this->state = $state;
        $this->pluginRepository = $pluginRepository;
        $this->cacheDir = $cacheDir;
        $this->client = $client;
        $this->cacheIdLoader = $cacheIdLoader;
    }

    /**
     * @Route(path="/api/v{version}/_action/swag-security/available-fixes")
     */
    public function getFixes(): JsonResponse
    {
        return new JsonResponse([
            'availableFixes' => array_map(static function ($fix) {
                return $fix::getTicket();
            }, $this->state->getAvailableFixes()),
            'activeFixes' => array_map(static function ($fix) {
                return $fix::getTicket();
            }, $this->state->getActiveFixes())
        ]);
    }

    /**
     * @Route(path="/api/v{version}/_action/swag-security/update-available")
     */
    public function updateAvailable(Context $context): JsonResponse
    {
        $res = $this->client->get('https://api.shopware.com/pluginStore/pluginsByName?locale=en_GB&shopwareVersion=6.0.0&technicalNames=SwagPlatformSecurity');
        $apiResponse = json_decode((string) $res->getBody(), true);

        if (isset($apiResponse[0])) {
            $apiResponse = $apiResponse[0];
        } else {
            return new JsonResponse([
                'updateAvailable' => false
            ]);
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', SwagPlatformSecurity::PLUGIN_NAME));

        /** @var Plugin\PluginEntity $plugin */
        $plugin = $this->pluginRepository->search($criteria, $context)->first();

        $response = [];

        $response['updateAvailable'] = version_compare($apiResponse['version'], $plugin->getVersion(), '>');

        $usefulChangelogs = [];
        foreach ($apiResponse['changelog'] as $changelog) {
            if (version_compare($plugin->getVersion(), $changelog['version'], '<')) {
                $usefulChangelogs[] = $changelog;
            }
        }

        $response['changelog'] = $usefulChangelogs;

        return new JsonResponse($response);
    }

    /**
     * @Route(path="/api/v{version}/_action/swag-security/clear-container-cache")
     */
    public function clearContainerCache(): Response
    {
        $finder = (new Finder())->in($this->cacheDir)->name('*Container*')->depth(0);
        $containerCaches = [];

        foreach ($finder->getIterator() as $containerPaths) {
            $containerCaches[] = $containerPaths->getRealPath();
        }

        (new Filesystem())->remove($containerCaches);

        $this->cacheIdLoader->write(Uuid::randomHex());

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
