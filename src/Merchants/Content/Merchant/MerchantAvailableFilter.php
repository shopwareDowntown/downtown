<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

class MerchantAvailableFilter extends MultiFilter
{
    public function __construct(string $salesChannelId, string $prefix = '')
    {
        if ($prefix !== '') {
            $prefix = rtrim($prefix, '.') . '.';
        }

        parent::__construct(
            self::CONNECTION_AND,
            [
                new EqualsFilter($prefix . 'salesChannelId', $salesChannelId),
                new EqualsFilter($prefix . 'active', true),
                new EqualsFilter($prefix . 'public', true),
            ]
        );
    }
}
