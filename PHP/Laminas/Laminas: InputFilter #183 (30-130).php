    }

    /**
     * Get factory to use when adding inputs and filters by spec
     *
     * Lazy-loads a Factory instance if none attached.
     *
     * @return Factory
     */
    public function getFactory()
    {
        if (null === $this->factory) {
            $this->factory = new Factory();
        }
        return $this->factory;
    }

    /**
     * Add an input to the input filter
     *
     * @param  InputSpecification|Traversable|InputInterface|InputFilterInterface $input
     * @param  array-key|null $name
     * @return $this
     */
    public function add($input, $name = null)
    {
        if (
            is_array($input)
            || ($input instanceof Traversable && ! $input instanceof InputFilterInterface)
        ) {
            $factory = $this->getFactory();
            $input   = $factory->createInput($input);
        }

        // At this point $input is potentially invalid. parent::add() will throw an exception in this case.

        parent::add($input, $name);

        return $this;
    }
}
