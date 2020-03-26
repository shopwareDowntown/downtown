<?php declare(strict_types=1);

namespace Shopware\Production\LocalDelivery\DeliveryRoute\Services;

use GuzzleHttp\Client;

class MapboxService
{
    private const ENDPOINT = 'mapbox.places';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $mapboxApiKey;

    public function __construct()
    {
        $this->mapboxApiKey = getenv('MAPBOX_API_KEY');
        $this->client = new Client([
            'base_uri' => 'https://api.mapbox.com',
            'timeout' => 2.0
        ]);
    }

    /**
     * @param string $searchtext
     * @return array [long, lat]
     * @throws \Exception
     */
    public function getGpsCoordinates(string $searchtext) : array
    {
        $response = $this->client->get('/geocoding/v5/' . self::ENDPOINT . '/' . $searchtext . '.json', [
            'query' => [
                'access_token' => $this->mapboxApiKey,
                'limit' => 1,
                'types' => 'address'
            ]
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Error from mapbox");
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['features'])) {
            throw new \Exception("No coordinates found.");
        }

        return $data['features'][0]['center'];
    }
}
