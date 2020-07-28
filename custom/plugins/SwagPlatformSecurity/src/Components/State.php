<?php declare(strict_types=1);

namespace Swag\Security\Components;

class State
{
    public const CONFIG_PREFIX = 'SwagPlatformSecurity.config.';

    /**
     * @var AbstractSecurityFix[]
     */
    public const KNOWN_ISSUES = [
        \Swag\Security\Fixes\NEXT9241\SecurityFix::class,
        \Swag\Security\Fixes\NEXT9240\SecurityFix::class,
        \Swag\Security\Fixes\NEXT9175\SecurityFix::class,
        \Swag\Security\Fixes\NEXT9242\SecurityFix::class,
        \Swag\Security\Fixes\NEXT9243\SecurityFix::class,
        \Swag\Security\Fixes\NEXT9569\SecurityFix::class,
    ];

    /**
     * @var AbstractSecurityFix[]
     */
    private $activeFixes;

    /**
     * @var AbstractSecurityFix[]
     */
    private $availableFixes;

    public function __construct(array $availableFixes, array $activeFixes)
    {
        $this->availableFixes = $availableFixes;
        $this->activeFixes = $activeFixes;
    }

    public function getActiveFixes(): array
    {
        return $this->activeFixes;
    }

    public function getAvailableFixes(): array
    {
        return $this->availableFixes;
    }

    public function isActive(string $ticket): bool
    {
        foreach ($this->getActiveFixes() as $validFix) {
            if ($validFix::getTicket() === $ticket) {
                return true;
            }
        }

        return false;
    }
}
