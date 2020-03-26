<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Content\Merchant\Storefront\Service;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class MerchantProductCriteriaLoader implements MerchantCriteriaLoaderInterface
{

    public function getMerchantCriteria(Criteria $criteria): Criteria
    {
        $criteria->addAssociation('products');
        $criteria->addFilter(new EqualsFilter('public', 1));
    }
}
