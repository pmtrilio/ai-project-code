                // static text
                $url = $token[1].$url;
                $optional = false;
            }
        }

        if ('' === $url) {
            $url = '/';
        }

        // the contexts base URL is already encoded (see Symfony\Component\HttpFoundation\Request)
        $url = strtr(rawurlencode($url), $this->decodedChars);

        // the path segments "." and ".." are interpreted as relative reference when resolving a URI; see http://tools.ietf.org/html/rfc3986#section-3.3
        // so we need to encode them as they are not used for this purpose here
        // otherwise we would generate a URI that, when followed by a user agent (e.g. browser), does not match this route
        $url = strtr($url, ['/../' => '/%2E%2E/', '/./' => '/%2E/']);
        if (str_ends_with($url, '/..')) {
            $url = substr($url, 0, -2).'%2E%2E';
        } elseif (str_ends_with($url, '/.')) {
            $url = substr($url, 0, -1).'%2E';
        }

        $schemeAuthority = '';
        $host = $this->context->getHost();
        $scheme = $this->context->getScheme();

        if ($requiredSchemes) {
            if (!\in_array($scheme, $requiredSchemes, true)) {
                $referenceType = self::ABSOLUTE_URL;
                $scheme = current($requiredSchemes);
            }
        }

        if ($hostTokens) {
            $routeHost = '';
            foreach ($hostTokens as $token) {
                if ('variable' === $token[0]) {
                    // check requirement (while ignoring look-around patterns)
                    if (null !== $this->strictRequirements && !preg_match('#^'.preg_replace('/\(\?(?:=|<=|!|<!)((?:[^()\\\\]+|\\\\.|\((?1)\))*)\)/', '', $token[2]).'$#i'.(empty($token[4]) ? '' : 'u'), $mergedParams[$token[3]])) {
                        if ($this->strictRequirements) {
                            throw new InvalidParameterException(strtr($message, ['{parameter}' => $token[3], '{route}' => $name, '{expected}' => $token[2], '{given}' => $mergedParams[$token[3]]]));
                        }

                        $this->logger?->error($message, ['parameter' => $token[3], 'route' => $name, 'expected' => $token[2], 'given' => $mergedParams[$token[3]]]);

                        return '';
                    }

                    $routeHost = $token[1].$mergedParams[$token[3]].$routeHost;
                } else {
                    $routeHost = $token[1].$routeHost;
                }
            }

            if ($routeHost !== $host) {
                $host = $routeHost;