        }
        if (empty(static::$supported_algs[$header->alg])) {
            throw new UnexpectedValueException('Algorithm not supported');
        }

        $key = self::getKey($keyOrKeyArray, property_exists($header, 'kid') ? $header->kid : null);

        // Check the algorithm
        if (!self::constantTimeEquals($key->getAlgorithm(), $header->alg)) {
            // See issue #351
            throw new UnexpectedValueException('Incorrect key for this algorithm');
        }
        if (\in_array($header->alg, ['ES256', 'ES256K', 'ES384'], true)) {
            // OpenSSL expects an ASN.1 DER sequence for ES256/ES256K/ES384 signatures
            $sig = self::signatureToDER($sig);
        }
        if (!self::verify("{$headb64}.{$bodyb64}", $sig, $key->getKeyMaterial(), $header->alg)) {
            throw new SignatureInvalidException('Signature verification failed');
        }

        // Check the nbf if it is defined. This is the time that the
        // token can actually be used. If it's not yet that time, abort.
        if (isset($payload->nbf) && floor($payload->nbf) > ($timestamp + static::$leeway)) {
            $ex = new BeforeValidException(
                'Cannot handle token with nbf prior to ' . \date(DateTime::ATOM, (int) floor($payload->nbf))
            );
            $ex->setPayload($payload);
            throw $ex;
        }

        // Check that this token has been created before 'now'. This prevents
        // using tokens that have been created for later use (and haven't
        // correctly used the nbf claim).
        if (!isset($payload->nbf) && isset($payload->iat) && floor($payload->iat) > ($timestamp + static::$leeway)) {
            $ex = new BeforeValidException(
                'Cannot handle token with iat prior to ' . \date(DateTime::ATOM, (int) floor($payload->iat))
            );
            $ex->setPayload($payload);
            throw $ex;
        }

        // Check if this token has expired.
        if (isset($payload->exp) && ($timestamp - static::$leeway) >= $payload->exp) {
            $ex = new ExpiredException('Expired token');
            $ex->setPayload($payload);
            $ex->setTimestamp($timestamp);
            throw $ex;
        }

        return $payload;
    }

    /**
     * Converts and signs a PHP array into a JWT string.
     *
     * @param array<mixed>          $payload PHP array
     * @param string|OpenSSLAsymmetricKey|OpenSSLCertificate $key The secret key.