            if ($this->key->isExpired()) {
                try {
                    $this->release();
                } catch (\Exception) {
                    // swallow exception to not hide the original issue
                }
                throw new LockExpiredException(\sprintf('Failed to put off the expiration of the "%s" lock within the specified time.', $this->key));
            }

            $this->logger?->debug('Expiration defined for "{resource}" lock for "{ttl}" seconds.', ['resource' => $this->key, 'ttl' => $ttl]);
        } catch (LockConflictedException $e) {
            $this->dirty = false;
            $this->logger?->notice('Failed to define an expiration for the "{resource}" lock, someone else acquired the lock.', ['resource' => $this->key]);
            throw $e;
        } catch (\Exception $e) {
            $this->logger?->notice('Failed to define an expiration for the "{resource}" lock.', ['resource' => $this->key, 'exception' => $e]);
            throw new LockAcquiringException(\sprintf('Failed to define an expiration for the "%s" lock.', $this->key), 0, $e);
        }
    }

    public function isAcquired(): bool
    {
        return $this->dirty = $this->store->exists($this->key);
    }

    public function release(): void
    {
        try {
            try {
                $this->store->delete($this->key);
                $this->dirty = false;
            } catch (LockReleasingException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new LockReleasingException(\sprintf('Failed to release the "%s" lock.', $this->key), 0, $e);
            }

            if ($this->store->exists($this->key)) {
                throw new LockReleasingException(\sprintf('Failed to release the "%s" lock, the resource is still locked.', $this->key));
            }

            $this->logger?->debug('Successfully released the "{resource}" lock.', ['resource' => $this->key]);
        } catch (LockReleasingException $e) {
            $this->logger?->notice('Failed to release the "{resource}" lock.', ['resource' => $this->key]);
            throw $e;
        }
    }

    public function isExpired(): bool
    {
        return $this->key->isExpired();
    }

    public function getRemainingLifetime(): ?float
    {
        return $this->key->getRemainingLifetime();
    }
}
