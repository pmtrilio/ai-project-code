                    'Unable to resolve the dispatcher from the service container. Please bind it or install the illuminate/bus package.',
                    is_int($e->getCode()) ? $e->getCode() : 0, $e
                );
            }
        }

        return $this->dispatcher;
    }

    /**
     * Dynamically handle calls into the schedule instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if (method_exists(PendingEventAttributes::class, $method)) {
            $this->attributes ??= $this->groupStack ? clone array_last($this->groupStack) : new PendingEventAttributes($this);

            return $this->attributes->$method(...$parameters);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }
}
