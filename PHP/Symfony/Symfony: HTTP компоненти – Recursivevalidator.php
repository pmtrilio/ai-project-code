<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Validator;

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextFactoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\MetadataInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * Recursive implementation of {@link ValidatorInterface}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class RecursiveValidator implements ValidatorInterface
{
    protected $contextFactory;
    protected $metadataFactory;
    protected $validatorFactory;
    protected $objectInitializers;
    protected ?ContainerInterface $groupProviderLocator;

    /**
     * Creates a new validator.
     *
     * @param ObjectInitializerInterface[] $objectInitializers The object initializers
     */
    public function __construct(ExecutionContextFactoryInterface $contextFactory, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, array $objectInitializers = [], ?ContainerInterface $groupProviderLocator = null)
    {
        $this->contextFactory = $contextFactory;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->objectInitializers = $objectInitializers;
        $this->groupProviderLocator = $groupProviderLocator;
    }

    public function startContext(mixed $root = null): ContextualValidatorInterface
    {
        return new RecursiveContextualValidator(
            $this->contextFactory->createContext($this, $root),
            $this->metadataFactory,
            $this->validatorFactory,
            $this->objectInitializers,
            $this->groupProviderLocator,
        );