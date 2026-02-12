<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * This implementation uses the '_controller' request attribute to determine
 * the controller to execute.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class ControllerResolver implements ControllerResolverInterface
{
    private ?LoggerInterface $logger;
    private array $allowedControllerTypes = [];
    private array $allowedControllerAttributes = [AsController::class => AsController::class];

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<class-string> $types
     * @param array<class-string> $attributes
     */
    public function allowControllers(array $types = [], array $attributes = []): void