        private void ProtectMemory()
        {
            Debug.Assert(_buffer != null);
            Debug.Assert(!_buffer.IsInvalid, "Invalid buffer!");

            if (_decryptedLength != 0 &&
                !_encrypted &&
                !Interop.Crypt32.CryptProtectMemory(_buffer, (uint)_buffer.ByteLength, Interop.Crypt32.CRYPTPROTECTMEMORY_SAME_PROCESS))
            {
                throw new CryptographicException(Marshal.GetLastPInvokeError());
            }

            _encrypted = true;
        }

        private void UnprotectMemory()
        {
            Debug.Assert(_buffer != null);
            Debug.Assert(!_buffer.IsInvalid, "Invalid buffer!");

            if (_decryptedLength != 0 &&
                _encrypted &&
                !Interop.Crypt32.CryptUnprotectMemory(_buffer, (uint)_buffer.ByteLength, Interop.Crypt32.CRYPTPROTECTMEMORY_SAME_PROCESS))
            {
                throw new CryptographicException(Marshal.GetLastPInvokeError());
            }

            _encrypted = false;
        }
    }
}
