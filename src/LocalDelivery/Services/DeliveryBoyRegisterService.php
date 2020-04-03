<?php declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopware\Production\LocalDelivery\Services;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryBoyRegisterService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryBoyRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityRepositoryInterface $deliveryBoyRepository, ValidatorInterface $validator)
    {
        $this->deliveryBoyRepository = $deliveryBoyRepository;
        $this->validator = $validator;
    }

    public function saveDeliveryBoy(array $data, Context $salesChannelContext): void
    {
        $this->deliveryBoyRepository->create($data, $salesChannelContext);
    }

    public function getDeliveryBoyDataFromRequest(Request $request): array
    {
        return [
            'title' => $request->request->get('title'),
            'firstName' => $request->request->get('firstname'),
            'lastName' => $request->request->get('lastname'),
            'street' => $request->request->get('street'),
            'email' => $request->request->get('email'),
            'zipcode' => $request->request->get('zipcode'),
            'city' => $request->request->get('city'),
            'phoneNumber' => $request->request->get('phone_number'),
            'password' => $request->request->get('password'),
            'password_confirm' => $request->request->get('password_confirm'),
        ];
    }

    public function validateDeliveryBoyData(array $deliveryBoy): ConstraintViolationListInterface
    {
        $constraints = $this->getConstrains($deliveryBoy['password']);

        return $this->validator->validate($deliveryBoy, $constraints);
    }

    public function getViolationMessages(ConstraintViolationListInterface $violationList): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $errorMessages = [];
        foreach ($violationList as $violation) {
            $accessor->setValue(
                $errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage()
            );
        }

        if (isset($errorMessages['password_confirm'])) {
            $errorMessages['password_confirm'] = 'The passwords are not identical';
        }

        return $errorMessages;
    }

    private function getConstrains(string $expectedPassword): Collection
    {
        return new Collection([
            'title' => [],
            'firstName' => [new Length(['min' => 2]), new NotBlank()],
            'lastName' => [new Length(['min' => 2]), new NotBlank()],
            'street' => [new Length(['min' => 2]), new NotBlank()],
            'email' => [new Email(), new NotBlank()],
            'zipcode' => [new Length(['min' => 5]), new NotBlank()],
            'city' => [new Length(['min' => 2]), new NotBlank()],
            'phoneNumber' => [new NotBlank()],
            'password' => [new Length(['min' => 8]), new NotBlank()],
            'password_confirm' => [new IdenticalTo($expectedPassword)],
        ]);
    }
}
