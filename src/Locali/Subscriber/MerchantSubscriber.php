<?php
declare(strict_types=1);

namespace Shopware\Production\Locali\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Shopware\Production\Locali\Services\LocaliApi;
use Shopware\Production\Locali\Transformer\OfferTransformer;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MerchantSubscriber
 * @package Shopware\Production\Locali\Subscriber
 */
class MerchantSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepository
     */
    private $merchantRepository;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    /**
     * @var LocaliApi
     */
    private $localiApi;

    /**
     * ProductSyncToLocaliSubscriber constructor.
     * @param EntityRepository $merchantRepository
     * @param EntityRepository $salesChannelRepository
     * @param LocaliApi $localiApi
     */
    public function __construct(
        EntityRepository $merchantRepository,
        EntityRepository $salesChannelRepository,
        LocaliApi $localiApi
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->localiApi = $localiApi;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'merchant.written' => 'onMerchantWritten',
            'merchant.deleted' => 'onMerchantDeleted',
        ];
    }

    /**
     * @param EntityWrittenEvent $event
     */
    public function onMerchantWritten(EntityWrittenEvent $event)
    {
        $writeResults = $event->getWriteResults();

        /** @var EntityWriteResult $writeResult */
        foreach ($writeResults as $writeResult) {
            $payload = $writeResult->getPayload();

            // avoid a recursive call during update operation
            if (!array_key_exists('localiOfferDocumentId', $payload)) {

                /** @var MerchantEntity $merchant */
                $merchant = $this->getMerchant($event, $writeResult->getPrimaryKey());

                if ($merchant->isPublic()) {
                    /** @var SalesChannelEntity $salesChannel */
                    $salesChannel = $this->getSalesChannel($event, $merchant);

                    if ($merchant && $salesChannel) {
                        $transformer = new OfferTransformer();
                        $offer = $transformer->transform(
                            $merchant,
                            $salesChannel->getPaymentMethods()
                        );

                        if (is_null($merchant->getLocaliOfferDocumentId())) {
                            $documentId = $this->localiApi->createOffer($offer);

                            if (is_string($documentId)) {
                                $this->updateMerchantByLocaliOfferDocumentId($event, $merchant, $documentId);
                            }
                        } else {
                            $this->localiApi->updateOffer($merchant->getLocaliOfferDocumentId(), $offer);
                        }
                    }
                } else {
                    $this->removeLocaliOfferIfDocumentIdIsGiven($event, $merchant);
                }
            }
        }
    }

    /**
     * @param EntityWrittenEvent $event
     * @param MerchantEntity $merchant
     * @param $documentId
     */
    private function updateMerchantByLocaliOfferDocumentId(
        EntityWrittenEvent $event,
        MerchantEntity $merchant,
        $documentId
    ) {
        $this->merchantRepository->update([
            [
                'id'                    => $merchant->getId(),
                'localiOfferDocumentId' => $documentId,
            ],
        ], $event->getContext());
    }

    /**
     * @param EntityWrittenEvent $event
     * @param string $primaryKey
     * @return MerchantEntity
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    private function getMerchant(
        EntityWrittenEvent $event,
        string $primaryKey
    ) {
        $criteria = new Criteria([$primaryKey]);
        $criteria->addAssociation('cover');

        /** @var MerchantEntity $merchant */
        $merchant = $this->merchantRepository->search($criteria, $event->getContext())->first();

        return $merchant;
    }

    /**
     * @param EntityWrittenEvent $event
     * @param MerchantEntity $merchant
     * @return SalesChannelEntity
     * @throws \Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException
     */
    private function getSalesChannel(
        EntityWrittenEvent $event,
        MerchantEntity $merchant
    ) {
        $criteria = new Criteria([$merchant->getSalesChannelId()]);
        $criteria->addAssociation('paymentMethods');

        /** @var SalesChannelEntity $salesChannel */
        $salesChannel = $this->salesChannelRepository->search($criteria, $event->getContext())->first();

        return $salesChannel;
    }

    /**
     * @param EntityDeletedEvent $event
     */
    public function onMerchantDeleted(
        EntityDeletedEvent $event
    ) {
        $writeResults = $event->getWriteResults();
        /** @var EntityWriteResult $writeResult */
        foreach ($writeResults as $writeResult) {
            /** @var MerchantEntity $merchant */
            $merchant = $this->getMerchant($event, $writeResult->getPrimaryKey());
            $this->removeLocaliOfferIfDocumentIdIsGiven($event, $merchant);
        }
    }

    /**
     * @param EntityWrittenEvent $event
     * @param MerchantEntity $merchant
     */
    private function removeLocaliOfferIfDocumentIdIsGiven(
        EntityWrittenEvent $event,
        MerchantEntity $merchant
    ) {
        $localiOfferDocumentId = $merchant->getLocaliOfferDocumentId();

        if (!is_null($localiOfferDocumentId)) {
            $this->localiApi->deleteOffer($localiOfferDocumentId);
            $this->updateMerchantByLocaliOfferDocumentId($event, $merchant, null);
        }
    }
}
