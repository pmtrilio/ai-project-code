            $this->expressionBuilder = new Query\Expr();
        }

        return $this->expressionBuilder;
    }

    public function getIdentifierFlattener() : IdentifierFlattener
    {
        return $this->identifierFlattener;
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritDoc}
     */
    public function transactional(callable $func)
    {
        $this->conn->beginTransaction();

        try {
            $return = $func($this);

            $this->flush();
            $this->conn->commit();

            return $return;
        } catch (Throwable $e) {
            $this->close();
            $this->conn->rollBack();

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollback()
    {
        $this->conn->rollBack();
    }

    /**
     * Returns the ORM metadata descriptor for a class.