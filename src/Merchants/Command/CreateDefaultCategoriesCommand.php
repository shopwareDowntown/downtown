<?php

namespace Shopware\Production\Merchants\Command;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDefaultCategoriesCommand extends Command
{
    public static $defaultName = 'create:default:categories';
    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(EntityRepositoryInterface $salesChannelRepository, EntityRepositoryInterface $categoryRepository)
    {
        parent::__construct();
        $this->salesChannelRepository = $salesChannelRepository;
        $this->categoryRepository = $categoryRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = [
            0 => 'Travel, rental and transportation',
            1 => 'Books, magazines and newspapers',
            2 => 'General merchandise',
            3 => 'Food and drinks',
            4 => 'Automotive Products',
            5 => 'Children Products',
            6 => 'Clothing & Shoes',
            7 => 'Electronics, computers and software',
            8 => 'Entertainment',
            9 => 'Digital services',
            10 => 'Jewelry & Accessories',
            11 => 'Health & Beauty products',
            12 => 'Financial services',
            13 => 'Personal services',
            14 => 'Events, festivals and recreation',
            15 => 'Charity and donations',
            16 => 'Other',
        ];

        $parentId = $this->getDefaultCategoryId();

        $items = [];
        foreach ($categories as $name) {
            $items[] = [
                'name' => $name,
                'active' => true,
                'parentId' => $parentId,
            ];
        }

        $this->categoryRepository->create($items, Context::createDefaultContext());

        return 0;
    }

    private function getDefaultCategoryId(): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('typeId', Defaults::SALES_CHANNEL_TYPE_STOREFRONT));
        return $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->first()->getNavigationCategoryId();
    }
}
