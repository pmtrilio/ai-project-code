<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Definition;

/**
 * This class is the entry point for config normalization/merging/finalization.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @final
 */
class Processor
{
    /**
     * Processes an array of configurations.
     *
     * @param array $configs An array of configuration items to process
     */
    public function process(NodeInterface $configTree, array $configs): array
    {
        $currentConfig = [];
        foreach ($configs as $config) {
            $config = $configTree->normalize($config);
            $currentConfig = $configTree->merge($currentConfig, $config);
        }

        return $configTree->finalize($currentConfig);
    }

    /**
     * Processes an array of configurations.
     *