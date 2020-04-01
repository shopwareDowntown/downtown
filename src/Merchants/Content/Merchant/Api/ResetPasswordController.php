<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use Shopware\Core\Content\MailTemplate\Service\MailSender;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @RouteScope(scopes={"merchant-api"})
 */
class ResetPasswordController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $merchantRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $merchantResetPasswordTokenRepository;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var MailSender
     */
    private $mailService;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        EntityRepositoryInterface $merchantResetPasswordTokenRepository,
        Environment $twig,
        MailSender $mailService,
        SystemConfigService $systemConfigService
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->merchantResetPasswordTokenRepository = $merchantResetPasswordTokenRepository;
        $this->twig = $twig;
        $this->mailService = $mailService;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route(name="merchant-api.account.password.reset", path="/merchant-api/v{version}/reset-password", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function reset(RequestDataBag $dataBag, SalesChannelContext $context): Response
    {
        $successResponse = new JsonResponse(['success' => true]);

        if (!$dataBag->has('email')) {
            throw new \InvalidArgumentException('Missing email request argument');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $dataBag->get('email')));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$merchant) {
            return $successResponse;
        }

        $token = Random::getAlphanumericString(16);

        $this->merchantRepository->update([
            [
                'id' => $merchant->getId(),
                'resetPasswordTokens' => [
                    [
                        'token' => $token
                    ]
                ],
            ]
        ], $context->getContext());

        $this->sendRecoveryMail($token, $merchant, $context);

        return $successResponse;
    }

    /**
     * @Route(name="merchant-api.account.password.confirm", path="/merchant-api/v{version}/reset-password-confirm", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function resetConfirm(RequestDataBag $dataBag, SalesChannelContext $context): Response
    {
        if (!$dataBag->has('token') || !$dataBag->has('newPassword')) {
            throw new \InvalidArgumentException('Missing token or newPassword request argument');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('resetPasswordTokens.token', $dataBag->get('token')));

        /** @var MerchantEntity|null $merchant */
        $merchant = $this->merchantRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$merchant) {
            return new JsonResponse(['success' => false]);
        }

        $this->merchantRepository->update([
            [
                'id' => $merchant->getId(),
                'password' => $dataBag->get('newPassword')
            ]
        ], $context->getContext());

        $this->cleanupOldTokens($merchant);

        return new JsonResponse(['success' => true]);
    }

    private function cleanupOldTokens(MerchantEntity $merchant): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('merchantId', $merchant->getId()));

        $ids = $this->merchantResetPasswordTokenRepository->searchIds($criteria, Context::createDefaultContext())->getIds();

        if (!\count($ids)) {
            return;
        }

        $this->merchantResetPasswordTokenRepository->delete(array_map(static function (string $id) {
            return ['id' => $id];
        }, $ids), Context::createDefaultContext());
    }

    private function sendRecoveryMail(string $token, MerchantEntity $merchant, SalesChannelContext $context): void
    {
        $html = $this->twig->render('@Merchant/email/merchant_reset_password.html.twig', [
            'merchant' => $merchant,
            'salesChannel' => $context->getSalesChannel(),
            'urlResetPassword' => getenv('MERCHANT_PORTAL') . '/reset-password/' . $token
        ]);

        $senderEmail = $this->systemConfigService->get('core.basicInformation.email');

        $mail = new \Swift_Message('Passwort zurücksetzen Anfrage');
        $mail->addTo($merchant->getEmail(), $merchant->getPublicCompanyName());
        $mail->addFrom($senderEmail);
        $mail->setBody($html, 'text/html');

        $this->mailService->send($mail);
    }
}
