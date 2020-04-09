<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Util\Random;
use Shopware\Production\Organization\OrganizationEvents;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrganizationCreateSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var TemplateMailSender
     */
    private $templateMailSender;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EntityRepositoryInterface $organizationRepository,
        TemplateMailSender $templateMailSender,
        TranslatorInterface $translator
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->templateMailSender = $templateMailSender;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrganizationEvents::ORGANIZATION_WRITTEN_EVENT => 'organizationEventWritten'
        ];
    }

    public function organizationEventWritten(EntityWrittenEvent $event): void
    {
        foreach ($event->getWriteResults() as $writeResult) {
            if ($writeResult->getOperation() === 'insert') {
                $this->resetPasswordAndSendMail($writeResult->getPrimaryKey(), $event->getContext());
            }
        }
    }

    private function resetPasswordAndSendMail(string $id, Context $context): void
    {
        $password = Random::getAlphanumericString(8);

        $this->organizationRepository->update([
            [
                'id' => $id,
                'password' => $password
            ]
        ], $context);

        $organization = $this->fetchOrganization($id, $context);
        $this->fixTranslator($organization);

        $this->templateMailSender->sendMail(
            $organization->getEmail(),
            'organization_registration',
            [
                'organization' => $organization,
                'password' => $password,
                'loginLink' => getenv('MERCHANT_PORTAL')
            ]
        );
    }

    private function fetchOrganization(string $id, Context $context): OrganizationEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('salesChannel.language.locale');

        return $this->organizationRepository->search($criteria, $context)->first();
    }

    private function fixTranslator(OrganizationEntity $organization): void
    {
        $this->translator->injectSettings(
            $organization->getSalesChannelId(),
            $organization->getSalesChannel()->getLanguageId(),
            $organization->getSalesChannel()->getLanguage()->getLocale()->getCode(),
            Context::createDefaultContext()
        );
    }
}
