                Event\Code\ThrowableBuilder::from($e),
            );
        }

        // Tear down the fixture. An exception raised in tearDown() will be
        // caught and passed on when no exception was raised before.
        try {
            if ($hasMetRequirements) {
                $this->invokeAfterTestHookMethods($hookMethods, $emitter);

                if ($this->inIsolation) {
                    // @codeCoverageIgnoreStart
                    $this->invokeAfterClassHookMethods($hookMethods, $emitter);
                    // @codeCoverageIgnoreEnd
                }
            }
        } catch (AssertionError|AssertionFailedError $e) {
            $this->status = TestStatus::failure($e->getMessage());

            $emitter->testFailed(
                $this->valueObjectForEvents(),
                Event\Code\ThrowableBuilder::from($e),
                Event\Code\ComparisonFailureBuilder::from($e),
            );
        } catch (Throwable $exceptionRaisedDuringTearDown) {
            if (!isset($e) || $e instanceof SkippedWithMessageException) {
                $this->status = TestStatus::error($exceptionRaisedDuringTearDown->getMessage());
                $e            = $exceptionRaisedDuringTearDown;

                $emitter->testErrored(
                    $this->valueObjectForEvents(),
                    Event\Code\ThrowableBuilder::from($exceptionRaisedDuringTearDown),
                );
            }
        }

        if (!isset($e) && !isset($_e)) {
            $emitter->testPassed(
                $this->valueObjectForEvents(),
            );

            if (!$this->usesDataProvider()) {
                PassedTests::instance()->testMethodPassed(
                    $this->valueObjectForEvents(),
                    $this->testResult,
                );
            }
        }

        if (!$outputBufferingStopped) {
            $this->stopOutputBuffering();
        }

        clearstatcache();

        if ($currentWorkingDirectory !== false && $currentWorkingDirectory !== getcwd()) {
            chdir($currentWorkingDirectory);
        }

        $this->restoreEnvironmentVariables();
        $this->restoreGlobalErrorExceptionHandlers();
        $this->restoreGlobalState();
        $this->unregisterCustomComparators();
        libxml_clear_errors();

        $this->testValueObjectForEvents = null;

        if (isset($e)) {
            $this->onNotSuccessfulTest($e);
        }
    }

    /**
     * @param list<ExecutionOrderDependency> $dependencies
     *
     * @internal This method is not covered by the backward compatibility promise for PHPUnit
     */
    final public function setDependencies(array $dependencies): void