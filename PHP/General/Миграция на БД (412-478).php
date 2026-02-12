
        if ($pretend) {
            return $this->pretendToRun($instance, 'down');
        }

        $this->write(Task::class, $name, fn () => $this->runMigration($instance, 'down'));

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($migration);
    }

    /**
     * Run a migration inside a transaction if the database supports it.
     *
     * @param  object  $migration
     * @param  string  $method
     * @return void
     */
    protected function runMigration($migration, $method)
    {
        $connection = $this->resolveConnection(
            $migration->getConnection()
        );

        $callback = function () use ($connection, $migration, $method) {
            if (method_exists($migration, $method)) {
                $this->fireMigrationEvent(new MigrationStarted($migration, $method));

                $this->runMethod($connection, $migration, $method);

                $this->fireMigrationEvent(new MigrationEnded($migration, $method));
            }
        };

        $this->getSchemaGrammar($connection)->supportsSchemaTransactions()
            && $migration->withinTransaction
                ? $connection->transaction($callback)
                : $callback();
    }

    /**
     * Pretend to run the migrations.
     *
     * @param  object  $migration
     * @param  string  $method
     * @return void
     */
    protected function pretendToRun($migration, $method)
    {
        $name = get_class($migration);

        $reflectionClass = new ReflectionClass($migration);

        if ($reflectionClass->isAnonymous()) {
            $name = $this->getMigrationName($reflectionClass->getFileName());
        }

        $this->write(TwoColumnDetail::class, $name);

        $this->write(
            BulletList::class,
            (new Collection($this->getQueries($migration, $method)))->map(fn ($query) => $query['query'])
        );
    }
