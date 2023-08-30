<?php
declare(strict_types=1);

namespace Shopware\Production\Locali\Services;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Shopware\Production\Locali\Common\Http\CurlClient;
use Shopware\Production\Locali\Model\Offer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LocaliApi
 * @package Shopware\Production\Locali\Services
 */
class LocaliApi
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $authKey;

    /**
     * @var CurlClient
     */
    private $client;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    const CREATE_ENDPOINT = 'offers';
    const UPDATE_ENDPOINT = 'offers/{offerId}';
    const DELETE_ENDPOINT = 'offers/{offerId}';

    /**
     * LocaliApi constructor.
     * @param string $baseUrl
     * @param string $authKey
     * @param LoggerInterface $logger |null
     */
    public function __construct(string $baseUrl, string $authKey = null, LoggerInterface $logger = null)
    {
        $this->baseUrl = $baseUrl;
        $this->authKey = $authKey;
        $this->client = new CurlClient();
        $this->logger = $logger;
    }

    /**
     * @param Offer $offer
     * @return string|null
     */
    public function createOffer(Offer $offer)
    {
        $url = $this->generateEndpointUrl(self::CREATE_ENDPOINT);

        return $this->sendRequest(Request::METHOD_POST, $url, $offer);
    }

    /**
     * @param string $documentId
     * @param Offer $offer
     * @return string|null
     */
    public function updateOffer(string $documentId, Offer $offer)
    {
        $url = $this->generateEndpointUrl(self::UPDATE_ENDPOINT);
        $url = str_replace('{offerId}', $documentId, $url);

        return $this->sendRequest(Request::METHOD_PATCH, $url, $offer);
    }

    /**
     * @param string $documentId
     * @return string|null
     */
    public function deleteOffer(string $documentId)
    {
        $url = $this->generateEndpointUrl(self::DELETE_ENDPOINT);
        $url = str_replace('{offerId}', $documentId, $url);

        // reminder: api implementation missing
        // $this->sendRequest(Request::METHOD_DELETE, $url);

        return null;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        if (!empty($this->authKey)) {
            return true;
        }

        return false;
    }

    /**
     * @param $method
     * @param $url
     * @param Offer $offer
     * @return string|void|null
     */
    private function sendRequest($method, $url, Offer $offer)
    {
        // avoid to call the api, if the auth key is not given
        if (!$this->isConfigured()) {
            if ($this->logger) {
                $this->logger->debug('Locali api service is not configured. Auth key must be defined.');
            }

            return null;
        }

        $method = strtolower($method);

        try {
            $response = $this->client->{$method}($url, $offer, $this->getHeaders());

            if ($response instanceof ResponseInterface) {
                return $this->getDocumentIdFromResponse($response);
            }
        } catch (\Exception $exception) {
            if ($this->logger) {
                $this->logger->error(
                    sprintf(
                        'Locali api throws an error. code: %d, message: %s',
                        $exception->getCode(),
                        $exception->getMessage()
                    )
                );
            }
        }

        return null;
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    private function getDocumentIdFromResponse(ResponseInterface $response): ?string
    {
        $content = $response->getBody()->getContents();

        $data = json_decode($content, true);

        if (isset($data['documentId']) && !empty($data['documentId'])) {
            return $data['documentId'];
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->authKey,
        ];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function generateEndpointUrl(string $endpoint)
    {
        return sprintf('%s%s', $this->baseUrl, $endpoint);
    }
}
