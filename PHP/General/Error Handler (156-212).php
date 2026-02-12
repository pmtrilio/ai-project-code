            return $this->exception->getCode();
        }

        return 500;
    }

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * Slim's error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     */
    protected function determineContentType(ServerRequestInterface $request): ?string
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        $selectedContentTypes = array_intersect(
            explode(',', $acceptHeader),
            array_keys($this->errorRenderers)
        );
        $count = count($selectedContentTypes);

        if ($count) {
            $current = current($selectedContentTypes);

            /**
             * Ensure other supported content types take precedence over text/plain
             * when multiple content types are provided via Accept header.
             */
            if ($current === 'text/plain' && $count > 1) {
                $next = next($selectedContentTypes);
                if (is_string($next)) {
                    return $next;
                }
            }

            // @phpstan-ignore-next-line
            if (is_string($current)) {
                return $current;
            }
        }

        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (array_key_exists($mediaType, $this->errorRenderers)) {
                return $mediaType;
            }
        }

        return null;
    }

    /**
     * Determine which renderer to use based on content type
     *
     * @throws RuntimeException
     */