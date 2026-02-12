
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