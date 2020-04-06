<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Services;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerDoubleOptInRegistrationEvent;
use Shopware\Core\Content\MailTemplate\Service\MailSender;
use Shopware\Core\Content\Newsletter\Exception\SalesChannelDomainNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Production\Merchants\Content\Merchant\Exception\EmailAlreadyExistsException;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Twig\Environment;

class RegistrationService
{
    /**
     * @var EntityRepository
     */
    private $merchantRepository;

    /**
     * @var DataValidator
     */
    private $dataValidator;

    /**
     * @var TemplateMailSender
     */
    private $templateMailSender;

    public function __construct(
        EntityRepository $merchantRepository,
        DataValidator $dataValidator,
        TemplateMailSender $templateMailSender
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->dataValidator = $dataValidator;
        $this->templateMailSender = $templateMailSender;
    }

    public function registerMerchant(array $parameters, SalesChannelContext $salesChannelContext): string
    {
        $violations = $this->dataValidator->getViolations($parameters, $this->createValidationDefinition($salesChannelContext));
        if ($violations->count()) {
            throw new ConstraintViolationException($violations, $parameters);
        }

        $parameters['id'] = Uuid::randomHex();

        if (!$this->isMailAvailable($parameters['email'])) {
            throw new EmailAlreadyExistsException('Email address is already taken');
        }

        $parameters['activationCode'] = Uuid::randomHex();

        $this->merchantRepository->create([$parameters], $salesChannelContext->getContext());

        $criteria = new Criteria([$parameters['id']]);

        $result = $this->merchantRepository->search($criteria, $salesChannelContext->getContext());
        /** @var MerchantEntity $merchant */
        $merchant = $result->first();

        $this->sendMail($merchant, $salesChannelContext);

        return $parameters['id'];
    }

    private function isMailAvailable(string $email): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $email));

        return $this->merchantRepository->searchIds($criteria, Context::createDefaultContext())->getTotal() === 0;
    }

    protected function createValidationDefinition(SalesChannelContext $salesChannelContext): DataValidationDefinition
    {
        return (new DataValidationDefinition())
            ->add('publicCompanyName', new Type('string'))
            ->add('email', new Email())
            ->add('salesChannelId', new EntityExists(['entity' => 'sales_channel', 'context' => $salesChannelContext->getContext()]))
            ->add('password', new NotBlank(), new Length(['min' => 8]));
    }

    private function sendMail(MerchantEntity $merchant, SalesChannelContext $context): void
    {
        $this->templateMailSender->sendMail($merchant->getEmail(), 'merchant_registration', [
            'merchant' => $merchant,
            'confirmUrl' => $this->getConfirmUrl($merchant, $context)
        ]);
    }

    private function getConfirmUrl(MerchantEntity $merchantEntity, SalesChannelContext $context): string
    {
        $domainUrl = $context->getSalesChannel()->getDomains()->first()->getUrl();

        return sprintf(
            $domainUrl . '/merchant/registration/confirm?hash=%s',
            $merchantEntity->getActivationCode()
        );
    }
}
