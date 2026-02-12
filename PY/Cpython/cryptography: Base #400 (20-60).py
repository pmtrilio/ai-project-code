        Processes the provided bytes through the cipher and returns the results
        as bytes.
        """

    @abc.abstractmethod
    def update_into(self, data: Buffer, buf: Buffer) -> int:
        """
        Processes the provided bytes and writes the resulting data into the
        provided buffer. Returns the number of bytes written.
        """

    @abc.abstractmethod
    def finalize(self) -> bytes:
        """
        Returns the results of processing the final block as bytes.
        """

    @abc.abstractmethod
    def reset_nonce(self, nonce: bytes) -> None:
        """
        Resets the nonce for the cipher context to the provided value.
        Raises an exception if it does not support reset or if the
        provided nonce does not have a valid length.
        """


class AEADCipherContext(CipherContext, metaclass=abc.ABCMeta):
    @abc.abstractmethod
    def authenticate_additional_data(self, data: Buffer) -> None:
        """
        Authenticates the provided bytes.
        """


class AEADDecryptionContext(AEADCipherContext, metaclass=abc.ABCMeta):
    @abc.abstractmethod
    def finalize_with_tag(self, tag: bytes) -> bytes:
        """
        Returns the results of processing the final block as bytes and allows
        delayed passing of the authentication tag.
        """