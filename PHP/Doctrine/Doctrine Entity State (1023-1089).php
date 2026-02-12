                $this->listenersInvoker->invoke($class, Events::postPersist, $entity, $eventArgs, $invoke);
            }
        }
    }

    /**
     * Executes all entity updates for entities of the specified type.
     *
     * @param ClassMetadata $class
     */
    private function executeUpdates($class)
    {
        $className        = $class->getClassName();
        $persister        = $this->getEntityPersister($className);
        $preUpdateInvoke  = $this->listenersInvoker->getSubscribedSystems($class, Events::preUpdate);
        $postUpdateInvoke = $this->listenersInvoker->getSubscribedSystems($class, Events::postUpdate);

        foreach ($this->entityUpdates as $oid => $entity) {
            if ($this->em->getClassMetadata(get_class($entity))->getClassName() !== $className) {
                continue;
            }

            if ($preUpdateInvoke !== ListenersInvoker::INVOKE_NONE) {
                $this->listenersInvoker->invoke($class, Events::preUpdate, $entity, new PreUpdateEventArgs($entity, $this->em, $this->getEntityChangeSet($entity)), $preUpdateInvoke);

                $this->recomputeSingleEntityChangeSet($class, $entity);
            }

            if (! empty($this->entityChangeSets[$oid])) {
                $persister->update($entity);
            }

            unset($this->entityUpdates[$oid]);

            if ($postUpdateInvoke !== ListenersInvoker::INVOKE_NONE) {
                $this->listenersInvoker->invoke($class, Events::postUpdate, $entity, new LifecycleEventArgs($entity, $this->em), $postUpdateInvoke);
            }
        }
    }

    /**
     * Executes all entity deletions for entities of the specified type.
     *
     * @param ClassMetadata $class
     */
    private function executeDeletions($class)
    {
        $className = $class->getClassName();
        $persister = $this->getEntityPersister($className);
        $invoke    = $this->listenersInvoker->getSubscribedSystems($class, Events::postRemove);

        foreach ($this->entityDeletions as $oid => $entity) {
            if ($this->em->getClassMetadata(get_class($entity))->getClassName() !== $className) {
                continue;
            }

            $persister->delete($entity);

            unset(
                $this->entityDeletions[$oid],
                $this->entityIdentifiers[$oid],
                $this->originalEntityData[$oid],
                $this->entityStates[$oid]
            );

            // Entity with this $oid after deletion treated as NEW, even if the $oid
            // is obtained by a new entity because the old one went out of scope.