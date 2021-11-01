<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2021 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $containerConfigurator->import(SetList::SYMFONY);
    $containerConfigurator->import(SetList::CLEAN_CODE);

    $parameters->set(Option::PATHS, [
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $parameters->set(Option::CACHE_DIRECTORY, __DIR__.'/.ecs_cache');
    $parameters->set(Option::PARALLEL, true);
};
