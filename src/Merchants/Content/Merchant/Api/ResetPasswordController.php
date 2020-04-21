<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Production\Merchants\Content\Merchant\MerchantEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TemplateMailSender
     */
    private $templateMailSender;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        EntityRepositoryInterface $merchantRepository,
        EntityRepositoryInterface $merchantResetPasswordTokenRepository,
        TemplateMailSender $templateMailSender,
        TranslatorInterface $translator
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->merchantResetPasswordTokenRepository = $merchantResetPasswordTokenRepository;
        $this->templateMailSender = $templateMailSender;
        $this->translator = $translator;
    }

    /**
     * @OA\Post(
     *      path="/reset-password",
     *      description="Reset",
     *      operationId="reset",
     *      @OA\Parameter(
     *         name="email",
     *         in="body",
     *         description="Email",
     *         @OA\Schema(type="string"),
     *      ),
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="merchant-api.account.password.reset", path="/merchant-api/v{version}/reset-password", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function reset(RequestDataBag $dataBag): Response
    {
        $successResponse = new JsonResponse(['success' => true]);

        if (!$dataBag->has('email')) {
            throw new \InvalidArgumentException('Missing email request argument');
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $dataBag->get('email')));
        $criteria->addAssociation('salesChannel.language.locale');

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
        ], Context::createDefaultContext());

        $this->sendRecoveryMail($token, $merchant);

        return $successResponse;
    }

    /**
     * @OA\Post(
     *      path="/reset-password-confirm",
     *      description="Reset password confirm",
     *      operationId="resetConfirm",
     *      @OA\Parameter(
     *         name="token",
     *         in="body",
     *         description="Token",
     *         @OA\Schema(type="string"),
     *      ),
     *      @OA\Parameter(
     *         name="newPassword",
     *         in="body",
     *         description="New password",
     *         @OA\Schema(type="string"),
     *      ),
     *      tags={"Merchant"},
     *      @OA\Response(
     *          response="200",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="merchant-api.account.password.confirm", path="/merchant-api/v{version}/reset-password-confirm", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function resetConfirm(RequestDataBag $dataBag): Response
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
        ], Context::createDefaultContext());

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

    private function sendRecoveryMail(string $token, MerchantEntity $merchant): void
    {
        $this->translator->injectSettings(
            $merchant->getSalesChannelId(),
            $merchant->getSalesChannel()->getLanguageId(),
            $merchant->getSalesChannel()->getLanguage()->getLocale()->getCode(),
            Context::createDefaultContext()
        );

        $this->templateMailSender->sendMail(
            $merchant->getEmail(),
            'merchant_reset_password',
            [
                'merchant' => $merchant,
                'confirmUrl' => getenv('MERCHANT_PORTAL') . '/reset-password/merchant/' . $token
            ]
        );
    }
}
