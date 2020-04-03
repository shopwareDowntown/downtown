<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Context;

class MapboxService
{
    /**
     * Mapbox specific endpoint that describes the api.
     * There is another option that is paid only.
     */
    private const ENDPOINT = 'mapbox.places';

    /**
     * @var MapApiRequestLimiterService
     */
    private $mapApiRequestLimiterService;

    /**
     * @var string
     */
    private $mapboxApiKey;

    /**
     * @var Client
     */
    private $client;

    public function __construct(MapApiRequestLimiterService $mapApiRequestLimiterService)
    {
        $this->mapApiRequestLimiterService = $mapApiRequestLimiterService;
        $this->mapboxApiKey = getenv('MAPBOX_API_KEY');
        $this->client = new Client([
            'base_uri' => 'https://api.mapbox.com',
            'timeout' => 2.0
        ]);
    }

    public function convertAddressToSearchTerm(string $zipCode, string $city, string $street, string $country = ''): string
    {
        return "${zipCode} ${city}, ${street}, ${country}";
    }

    /**
     * @param string  $searchtext
     * @param Context $context
     * @return array [long, lat]
     * @throws \Exception
     */
    public function getGpsCoordinates(string $searchtext, Context $context) : array
    {
        if (!$this->mapApiRequestLimiterService->increaseCount('search-temporary-geocoding-api', $context)) {
            throw new \RuntimeException('Map api limit reached for search-temporary-geocoding-api');
        }

        $response = $this->client->get('/geocoding/v5/' . self::ENDPOINT . '/' . $searchtext . '.json', [
            'query' => [
                'access_token' => $this->mapboxApiKey,
                'limit' => 1,
                'types' => 'address'
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error from mapbox');
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['features'])) {
            throw new \RuntimeException('No coordinates found.');
        }

        return $data['features'][0]['center'];
    }

    /**
     * @param array   $coordinatesArray
     * @param string  $profile
     * @param Context $context
     * @return array [[long, lat],...]
     * @throws \Exception
     */
    public function getOptimizedRoute(array $coordinatesArray, string $profile, Context $context): array
    {
        if (!$this->mapApiRequestLimiterService->increaseCount('navigation-optimization-api', $context)) {
            throw new \RuntimeException('Map api limit reached for navigation-optimization-api');
        }

        $coordinatesQueryString = '';

        foreach ($coordinatesArray as $index => $coordinates) {
            if($index !== 0) {
                $coordinatesQueryString .= ';';
            }
            $coordinatesQueryString .= $coordinates[0] . ',' . $coordinates[1];
        }

        $response = $this->client->get('/optimized-trips/v1/mapbox/' . $profile . '/' . $coordinatesQueryString, [
            'query' => [
                'access_token' => $this->mapboxApiKey
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error from mapbox');
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
