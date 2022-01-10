<?php
/*
 * This file is part of the flysystem-stream-wrapper package.
 *
 * (c) 2021-2022 m2m server software gmbh <tech@m2m.at>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);

    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_72);

    $containerConfigurator->import(SetList::PHP_81);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::PRIVATIZATION);
//    $containerConfigurator->import(SetList::TYPE_DECLARATION);
//    $containerConfigurator->import(SetList::TYPE_DECLARATION_STRICT);
//    $containerConfigurator->import(SetList::NAMING);
//    $containerConfigurator->import(SetList::EARLY_RETURN);
//    $containerConfigurator->import(SetList::CODING_STYLE);
//    $containerConfigurator->import(SetList::DEAD_CODE);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
