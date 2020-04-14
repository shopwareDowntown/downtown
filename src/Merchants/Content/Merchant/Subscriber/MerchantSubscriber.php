<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Subscriber;

use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Production\Merchants\Content\Merchant\MerchantDefinition;
use Shopware\Production\Merchants\MerchantEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MerchantSubscriber implements EventSubscriberInterface
{
    /**
     * @var CacheClearer
     */
    private $cache;

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'preValidate',
            MerchantEvents::MERCHANT_WRITTEN_EVENT => 'onMerchantWritten'
        ];
    }

    public function __construct(CacheClearer $cache)
    {
        $this->cache = $cache;
    }

    public function onMerchantWritten(EntityWrittenEvent $event): void
    {
        foreach ($event->getWriteResults() as $writeResult) {
            if ($changeSet = $writeResult->getChangeSet()) {
                if ($changeSet->hasChanged('active')  || $changeSet->hasChanged('public') || $changeSet->hasChanged('categoryId')) {
                    $this->cache->invalidateTags([
                        'sales_channel-' . Uuid::fromBytesToHex($changeSet->getBefore('sales_channel_id')), // Invalidate http cache for the given sales channel,
                        'category.id', // Invalidate category searcher
                        'entity_category' // Invalidate category reader
                    ]);
                }
            }
        }
    }

    public function preValidate(PreWriteValidationEvent $event): void
    {
        foreach ($event->getCommands() as $command) {
            if (($command instanceof UpdateCommand) && $command->getDefinition()->getEntityName() === MerchantDefinition::ENTITY_NAME) {
                if (isset($command->getPayload()['active']) || isset($command->getPayload()['public']) || isset($command->getPayload()['categoryId'])) {
                    $command->requestChangeSet();
                }
            }
        }
    }
}
