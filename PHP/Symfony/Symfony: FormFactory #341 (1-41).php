<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormFactory implements FormFactoryInterface
{
    private FormRegistryInterface $registry;

    public function __construct(FormRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function create(string $type = FormType::class, mixed $data = null, array $options = []): FormInterface
    {
        return $this->createBuilder($type, $data, $options)->getForm();
    }

    public function createNamed(string $name, string $type = FormType::class, mixed $data = null, array $options = []): FormInterface
    {
        return $this->createNamedBuilder($name, $type, $data, $options)->getForm();
    }

    public function createForProperty(string $class, string $property, mixed $data = null, array $options = []): FormInterface
    {
        return $this->createBuilderForProperty($class, $property, $data, $options)->getForm();
    }

    public function createBuilder(string $type = FormType::class, mixed $data = null, array $options = []): FormBuilderInterface