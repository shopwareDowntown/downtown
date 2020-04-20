<?php declare(strict_types=1);

namespace Shopware\Production\Organization\System\Organization\Api;

use OpenApi\Annotations as OA;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Production\Organization\System\Organization\OrganizationEntity;
use Shopware\Production\Portal\Services\TemplateMailSender;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @RouteScope(scopes={"organization-api"})
 */
class ResetPasswordController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $organizationResetPasswordRepository;

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
        EntityRepositoryInterface $organizationResetPasswordRepository,
        TemplateMailSender $templateMailSender,
        TranslatorInterface $translator
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->organizationResetPasswordRepository = $organizationResetPasswordRepository;
        $this->templateMailSender = $templateMailSender;
        $this->translator = $translator;
    }

    /**
     * @OA\Post(
     *      path="/reset-password",
     *      description="Reset password from organization",
     *      operationId="resetPassword",
     *      tags={"Organization"},
     *      @OA\Parameter(
     *        name="email",
     *        in="body",
     *        description="Email",
     *        @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.account.password", path="/organization-api/v{version}/reset-password", methods={"POST"}, defaults={"auth_required"=false})
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

        /** @var OrganizationEntity|null $organization */
        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$organization) {
            return $successResponse;
        }

        $token = Random::getAlphanumericString(16);

        $this->organizationRepository->update([
            [
                'id' => $organization->getId(),
                'resetPasswordTokens' => [
                    [
                        'token' => $token
                    ]
                ],
            ]
        ], Context::createDefaultContext());

        $this->sendRecoveryMail($token, $organization);

        return $successResponse;
    }

    /**
     * @OA\Post(
     *      path="/reset-password-confirm",
     *      description="Confirm reset password process",
     *      operationId="resetPasswordConfirm",
     *      tags={"Organization"},
     *      @OA\Parameter(
     *        name="token",
     *        in="body",
     *        description="Token",
     *        @OA\Schema(type="string"),
     *      ),
     *      @OA\Parameter(
     *        name="newPassword",
     *        in="body",
     *        description="newPassword",
     *        @OA\Schema(type="string"),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/definitions/SuccessResponse")
     *     )
     * )
     * @Route(name="organization-api.account.password.confirm", path="/organization-api/v{version}/reset-password-confirm", methods={"POST"}, defaults={"auth_required"=false})
     */
    public function resetConfirm(RequestDataBag $dataBag): Response
    {
        if (!$dataBag->has('token') || !$dataBag->has('newPassword')) {
            throw new \InvalidArgumentException('Missing token or newPassword request argument');
        }

        $criteria = new Criteria();
        $criteria->addAssociation('salesChannel.language.locale');
        $criteria->addFilter(new EqualsFilter('resetPasswordTokens.token', $dataBag->get('token')));

        /** @var OrganizationEntity|null $organization */
        $organization = $this->organizationRepository->search($criteria, Context::createDefaultContext())->first();

        if (!$organization) {
            return new JsonResponse(['success' => false]);
        }

        $this->organizationRepository->update([
            [
                'id' => $organization->getId(),
                'password' => $dataBag->get('newPassword')
            ]
        ], Context::createDefaultContext());

        $this->cleanupOldTokens($organization);

        return new JsonResponse(['success' => true]);
    }

    private function cleanupOldTokens(OrganizationEntity $organization): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('organizationId', $organization->getId()));

        $ids = $this->organizationResetPasswordRepository->searchIds($criteria, Context::createDefaultContext())->getIds();

        if (!\count($ids)) {
            return;
        }

        $this->organizationResetPasswordRepository->delete(array_map(static function (string $id) {
            return ['id' => $id];
        }, $ids), Context::createDefaultContext());
    }

    private function sendRecoveryMail(string $token, OrganizationEntity $organization): void
    {
        $this->translator->injectSettings(
            $organization->getSalesChannelId(),
            $organization->getSalesChannel()->getLanguageId(),
            $organization->getSalesChannel()->getLanguage()->getLocale()->getCode(),
            Context::createDefaultContext()
        );

        $this->templateMailSender->sendMail(
            $organization->getEmail(),
            'organization_reset_password',
            [
                'organization' => $organization,
                'confirmUrl' => getenv('MERCHANT_PORTAL') . '/reset-password/organization/' . $token
            ]
        );
    }
}
