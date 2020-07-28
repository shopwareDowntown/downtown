<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9242;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Shopware\Core\Framework\Api\OAuth\Scope\UserVerifiedScope;

class ScopeRepository extends \Shopware\Core\Framework\Api\OAuth\ScopeRepository
{
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null): array
    {
        $scopes = parent::finalizeScopes($scopes, $grantType, $clientEntity, $userIdentifier);

        if ($grantType !== 'password') {
            $scopes = $this->removeScope($scopes, UserVerifiedScope::class);
        }

        return $scopes;
    }

    private function removeScope(array $scopes, string $class): array
    {
        foreach ($scopes as $index => $scope) {
            if ($scope instanceof $class) {
                unset($scopes[$index]);
            }
        }

        return $scopes;
    }
}
