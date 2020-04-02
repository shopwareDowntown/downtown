<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Production\LocalDelivery\DeliveryBoy\DeliveryBoyEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeliveryBoyLoginService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryBoyRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var DeliveryBoySession
     */
    private $deliveryBoySession;

    public function __construct(
        EntityRepositoryInterface $deliveryBoyRepository,
        ValidatorInterface $validator,
        DeliveryBoySession $deliveryBoySession
    ) {
        $this->deliveryBoyRepository = $deliveryBoyRepository;
        $this->validator = $validator;
        $this->deliveryBoySession = $deliveryBoySession;
    }

    public function getDeliveryBoy(string $email, Context $context): ?DeliveryBoyEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $email));

        $searchResult = $this->deliveryBoyRepository->search($criteria, $context);

        if ($searchResult->getTotal() <= 0) {
            return null;
        }

        return $searchResult->first();
    }

    public function getLoginDataFromRequest(Request $request): array
    {
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
        ];
    }

    public function validateLoginData(array $loginData): ConstraintViolationListInterface
    {
        $constraints = new Collection([
            'email' => [new Email(), new NotBlank()],
            'password' => [new Length(['min' => 8]), new NotBlank()],
        ]);

        return $this->validator->validate($loginData, $constraints);
    }

    public function loginDeliveryBoy(DeliveryBoyEntity $deliveryBoyEntity, string $password, Context $context): bool
    {
        $isPasswordValid = password_verify($password, $deliveryBoyEntity->getPassword());

        if (!$isPasswordValid) {
            return $isPasswordValid;
        }

        $this->deliveryBoyRepository->update([[
            'id' => $deliveryBoyEntity->getId(),
            'sessionId' => $this->deliveryBoySession->getSessionId(),
        ]], $context);

        $this->deliveryBoySession->login($deliveryBoyEntity);

        return true;
    }

    public function isDeliveryBoyLoggedIn(Context $context): bool
    {
        $deliveryBoyData = $this->deliveryBoySession->getDeliveryBoyData();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('sessionId', $this->deliveryBoySession->getSessionId()));
        $criteria->addFilter(new EqualsFilter('id', $deliveryBoyData['id']));
        $criteria->addFilter(new EqualsFilter('email', $deliveryBoyData['email']));

        /** @var DeliveryBoyEntity|null $deliveryBoy */
        $deliveryBoy = $this->deliveryBoyRepository->search($criteria, $context)->first();
        if ($deliveryBoy === null || $deliveryBoyData['password'] !== $deliveryBoy->getPassword()) {
            $this->deliveryBoySession->logout();

            return false;
        }

        return true;
    }

    public function getDeliveryBoyId(): ?string
    {
        $deliveryBoyData = $this->deliveryBoySession->getDeliveryBoyData();

        return $deliveryBoyData['id'];
    }
}
