
        [Obsolete(Obsoletions.CryptoStringFactoryMessage, DiagnosticId = Obsoletions.CryptoStringFactoryDiagId, UrlFormat = Obsoletions.SharedUrlFormat)]
        [RequiresUnreferencedCode(CryptoConfig.CreateFromNameUnreferencedCodeMessage)]
        public static new Aes? Create(string algorithmName)
        {
            return (Aes?)CryptoConfig.CreateFromName(algorithmName);
        }

        /// <summary>
        ///   Computes the output length of the IETF RFC 5649 AES Key Wrap with Padding
        ///   Algorithm for the specified plaintext length.
        /// </summary>
        /// <param name="plaintextLengthInBytes">
        ///   The length of the plaintext to be wrapped, in bytes.
        /// </param>
        /// <returns>
        ///   The padded length of the key wrap for the specified plaintext.
        /// </returns>
        /// <exception cref="ArgumentOutOfRangeException">
        ///   <para>
        ///     <paramref name="plaintextLengthInBytes"/> is less than or equal to zero.
        ///   </para>
        ///   <para>-or-</para>
        ///   <para>
        ///     <paramref name="plaintextLengthInBytes"/> represents a plaintext length
        ///     that, when wrapped, has a length that cannot be represented as a signed
        ///     32-bit integer.
        ///   </para>
        /// </exception>
        public static int GetKeyWrapPaddedLength(int plaintextLengthInBytes)
        {
            ArgumentOutOfRangeException.ThrowIfNegativeOrZero(plaintextLengthInBytes);

            const int MaxSupportedValue = 0x7FFF_FFF0;

            if (plaintextLengthInBytes > MaxSupportedValue)
            {
                throw new ArgumentOutOfRangeException(
                    nameof(plaintextLengthInBytes),
                    SR.Cryptography_PlaintextTooLarge);
            }

            checked
            {
                int blocks = (plaintextLengthInBytes + 7) / 8;
                return (blocks + 1) * 8;
            }
        }

        /// <summary>
        ///   Wraps a key using the IETF RFC 5649 AES Key Wrap with Padding algorithm.
        /// </summary>
        /// <param name="plaintext">The data to wrap.</param>
        /// <returns>The wrapped data.</returns>
        /// <exception cref="ArgumentException"><paramref name="plaintext"/> is <see langword="null" /> or empty.</exception>
        /// <exception cref="CryptographicException">An error occurred during the cryptographic operation.</exception>
        public byte[] EncryptKeyWrapPadded(byte[] plaintext)
        {
            if (plaintext is null || plaintext.Length == 0)
                throw new ArgumentException(SR.Arg_EmptyOrNullArray, nameof(plaintext));

            return EncryptKeyWrapPadded(new ReadOnlySpan<byte>(plaintext));
        }

        /// <summary>
        ///   Wraps a key using the IETF RFC 5649 AES Key Wrap with Padding algorithm.
        /// </summary>
        /// <param name="plaintext">The data to wrap.</param>
        /// <returns>The wrapped data.</returns>
        /// <exception cref="ArgumentException"><paramref name="plaintext"/> is empty.</exception>
        /// <exception cref="CryptographicException">An error occurred during the cryptographic operation.</exception>
        public byte[] EncryptKeyWrapPadded(ReadOnlySpan<byte> plaintext)
        {
            if (plaintext.IsEmpty)
                throw new ArgumentException(SR.Arg_EmptySpan, nameof(plaintext));

            int outputLength = GetKeyWrapPaddedLength(plaintext.Length);
            byte[] output = new byte[outputLength];
            EncryptKeyWrapPaddedCore(plaintext, output);
            return output;
        }

        /// <summary>
        ///   Wraps a key using the IETF RFC 5649 AES Key Wrap with Padding algorithm,
        ///   writing the result to a specified buffer.
        /// </summary>
        /// <param name="plaintext">The data to wrap.</param>
        /// <param name="destination">The buffer to receive the wrapped data.</param>
        /// <exception cref="ArgumentException">
        ///   <para><paramref name="plaintext"/> is empty.</para>
        ///   <para>-or-</para>
        ///   <para><paramref name="destination"/> is not precisely sized.</para>
        /// </exception>
        /// <exception cref="CryptographicException">
        ///   <para><paramref name="plaintext"/> and <paramref name="destination"/> overlap.</para>
        ///   <para>-or-</para>
        ///   <para>An error occurred during the cryptographic operation.</para>
        /// </exception>
        /// <seealso cref="GetKeyWrapPaddedLength"/>
        public void EncryptKeyWrapPadded(ReadOnlySpan<byte> plaintext, Span<byte> destination)
        {