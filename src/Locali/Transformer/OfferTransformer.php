<?php
declare(strict_types=1);

namespace Shopware\Production\Locali\Transformer;

use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Production\Locali\Model\Address;
use Shopware\Production\Locali\Model\Contact;
use Shopware\Production\Locali\Model\Offer;
use Shopware\Production\Locali\Model\Organizer;
use Shopware\Production\Locali\Model\Url;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;

/**
 * Class OfferTransformer
 * @package Shopware\Production\Locali\Transformer
 */
class OfferTransformer
{
    /**
     * @param MerchantEntity $merchantEntity
     * @param PaymentMethodCollection $paymentMethods
     * @return Offer
     * @throws \Exception
     */
    public function transform(
        MerchantEntity $merchantEntity,
        PaymentMethodCollection $paymentMethods
    ) {
        $offer = new Offer();
        $tags = [];

        $address = new Address();
        $address
            ->setStreet($merchantEntity->getStreet())
            ->setCity($merchantEntity->getCity())
            ->setPostalCode($merchantEntity->getZip());

        $contact = new Contact();
        $contact
            ->setPhone($merchantEntity->getPublicPhoneNumber())
            ->setMobile(null)
            ->setFax(null)
            ->setMail($merchantEntity->getEmail());

        $organizer = new Organizer();
        $organizer->name = $merchantEntity->getPublicCompanyName();

        $offer
            ->setTitle($merchantEntity->getPublicCompanyName())
            ->setTags($tags)
            ->setContent($merchantEntity->getPublicDescription())
            ->setImageUrl($merchantEntity->getCover() ? $merchantEntity->getCover()->getUrl() : null)
            ->setAddress($address)
            ->setContact($contact)
            ->setOrganizer($organizer);

        if ($merchantEntity->getPublicWebsite()) {
            $url = new Url();
            $url->source = $merchantEntity->getPublicWebsite();
            $offer->setUrls([
                $url,
            ]);
        }

        $paymentMethodTypes = [];

        /** @var PaymentMethodEntity $paymentMethod */
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodTypes[] = $this->convertToCamelCase($paymentMethod->getName());
        }

        $offer->setAcceptedPaymentMethods($paymentMethodTypes);

        return $offer;
    }

    /**
     * @param string $string
     * @return string
     */
    private function convertToCamelCase(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords($string)));
    }
}
