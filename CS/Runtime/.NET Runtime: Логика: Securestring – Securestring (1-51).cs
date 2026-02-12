// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics;
using System.Runtime.InteropServices;
using System.Threading;

namespace System.Security
{
    public sealed partial class SecureString : IDisposable
    {
        private const int MaxLength = 65536;
        private readonly object _methodLock = new object();
        private UnmanagedBuffer? _buffer;
        private int _decryptedLength;
        private bool _encrypted;
        private bool _readOnly;

        public SecureString()
        {
            Initialize(ReadOnlySpan<char>.Empty);
        }

        [CLSCompliant(false)]
        public unsafe SecureString(char* value, int length)
        {
            ArgumentNullException.ThrowIfNull(value);

            ArgumentOutOfRangeException.ThrowIfNegative(length);
            ArgumentOutOfRangeException.ThrowIfGreaterThan(length, MaxLength);

            Initialize(new ReadOnlySpan<char>(value, length));
        }

        private void Initialize(ReadOnlySpan<char> value)
        {
            _buffer = UnmanagedBuffer.Allocate(GetAlignedByteSize(value.Length));
            _decryptedLength = value.Length;

            SafeBuffer? bufferToRelease = null;
            try
            {
                Span<char> span = AcquireSpan(ref bufferToRelease);
                value.CopyTo(span);
            }
            finally
            {
                ProtectMemory();
                bufferToRelease?.DangerousRelease();
            }
        }