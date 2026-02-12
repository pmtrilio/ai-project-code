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

        private SecureString(SecureString str)
        {
            Debug.Assert(str._buffer != null, "Expected other SecureString's buffer to be non-null");
            Debug.Assert(str._encrypted, "Expected to be used only on encrypted SecureStrings");

            _buffer = UnmanagedBuffer.Allocate((int)str._buffer.ByteLength);
            Debug.Assert(_buffer != null);
            UnmanagedBuffer.Copy(str._buffer, _buffer, str._buffer.ByteLength);

            _decryptedLength = str._decryptedLength;
            _encrypted = str._encrypted;
        }

        public int Length
        {
            get
            {
                EnsureNotDisposed();
                return Volatile.Read(ref _decryptedLength);
            }
        }

        private void EnsureCapacity(int capacity)
        {
            if (capacity > MaxLength)
            {
                throw new ArgumentOutOfRangeException(nameof(capacity), SR.ArgumentOutOfRange_Capacity);
            }

            Debug.Assert(_buffer != null);
            if ((uint)capacity * sizeof(char) <= _buffer.ByteLength)
            {
                return;