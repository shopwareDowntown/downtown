<?php declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT9175;

use Shopware\Core\Content\Media\File\FileUrlValidator;
use Shopware\Core\Content\Media\File\FileUrlValidatorInterface;
use Swag\Security\Components\State;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ReplaceFileFetcherCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(FileUrlValidatorInterface::class)) {
            $container->setDefinition(FileUrlValidatorInterface::class, new Definition(FileUrlValidator::class));
        }

        $def = $container->getDefinition(\Shopware\Core\Content\Media\File\FileFetcher::class);

        $def->setClass(FileFetcher::class);
        $def->setArguments([
            $def->getArguments(),
            new Reference(State::class),
            new Reference(FileUrlValidatorInterface::class)
        ]);
    }
}
