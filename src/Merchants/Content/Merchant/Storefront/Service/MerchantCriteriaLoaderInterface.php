<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Storefront\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

interface MerchantCriteriaLoaderInterface
{
    public function getMerchantCriteria(Criteria $criteria): Criteria;
}
