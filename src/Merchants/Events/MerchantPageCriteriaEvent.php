<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Events;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Contracts\EventDispatcher\Event;

class MerchantPageCriteriaEvent extends Event
{
    /**
     * @var Criteria
     */
    private $criteria;

    public function __construct(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }
}
