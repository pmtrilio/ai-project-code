
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


class AEADEncryptionContext(AEADCipherContext, metaclass=abc.ABCMeta):
    @property
    @abc.abstractmethod
    def tag(self) -> bytes:
        """
        Returns tag bytes. This is only available after encryption is
        finalized.
        """


Mode = typing.TypeVar(
    "Mode", bound=typing.Optional[modes.Mode], covariant=True
)


class Cipher(typing.Generic[Mode]):
    def __init__(
        self,
        algorithm: CipherAlgorithm,
        mode: Mode,
        backend: typing.Any = None,
    ) -> None:
        if not isinstance(algorithm, CipherAlgorithm):
            raise TypeError("Expected interface of CipherAlgorithm.")

        if mode is not None:
            # mypy needs this assert to narrow the type from our generic
            # type. Maybe it won't some time in the future.