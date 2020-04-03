<?php declare(strict_types=1);

namespace Shopware\Production\Merchants\Command;

use Shopware\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderEntity;
use Shopware\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDefaultMediaFolderCommand extends Command
{
    public static $defaultName = 'create:default:media:folder';

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaDefaultFolderRepository;

    public function __construct(EntityRepositoryInterface $mediaDefaultFolderRepository)
    {
        parent::__construct();
        $this->mediaDefaultFolderRepository = $mediaDefaultFolderRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->mediaDefaultFolderRepository->create([
            [
                'entity' => 'merchants',
                'associationFields' => [],
                'folder' => [
                    'name' => 'Merchant Cover Images',
                    'useParentConfiguration' => false,
                    'configuration' =>
                        [
                            'createThumbnails' => true,
                            'mediaThumbnailSizes' => [
                                [
                                    'width' => 960,
                                    'height' => 480
                                ]
                            ]
                        ]
                ]
            ]
        ], Context::createDefaultContext());

        $this->mediaDefaultFolderRepository->create([
            [
                'entity' => 'merchant_products',
                'associationFields' => ['productMedia'],
                'folder' => [
                    'name' => 'Merchant Product Images',
                    'useParentConfiguration' => false,
                    'configuration' =>
                        [
                            'createThumbnails' => true,
                            'mediaThumbnailSizes' => $this->getProductThumbnails()
                        ]
                ]
            ]
        ], Context::createDefaultContext());

        return 0;
    }

    private function getProductThumbnails(): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('entity', 'product'));
        $criteria->addAssociation('folder.configuration');

        /** @var MediaDefaultFolderEntity $folder */
        $folder = $this->mediaDefaultFolderRepository->search($criteria, Context::createDefaultContext())->first();

        return array_map(static function (MediaThumbnailSizeEntity $sizeEntity) {
            return ['id' => $sizeEntity->getId()];
        }, $folder->getFolder()->getConfiguration()->getMediaThumbnailSizes()->getElements());
    }
}
