from __future__ import annotations

import abc

from cryptography.hazmat.bindings._rust import openssl as rust_openssl
from cryptography.utils import Buffer

__all__ = [
    "MD5",
    "SHA1",
    "SHA3_224",
    "SHA3_256",
    "SHA3_384",
    "SHA3_512",
    "SHA224",
    "SHA256",
    "SHA384",
    "SHA512",
    "SHA512_224",
    "SHA512_256",
    "SHAKE128",
    "SHAKE256",
    "SM3",
    "BLAKE2b",
    "BLAKE2s",
    "ExtendableOutputFunction",
    "Hash",
    "HashAlgorithm",
    "HashContext",
    "XOFHash",
]


class HashAlgorithm(metaclass=abc.ABCMeta):
    @property
    @abc.abstractmethod
    def name(self) -> str:
        """
        A string naming this algorithm (e.g. "sha256", "md5").
        """

    @property
    @abc.abstractmethod
    def digest_size(self) -> int:
        """
        The size of the resulting digest in bytes.
        """

    @property
    @abc.abstractmethod
    def block_size(self) -> int | None:
        """
        The internal block size of the hash function, or None if the hash
        function does not use blocks internally (e.g. SHA3).
        """


class HashContext(metaclass=abc.ABCMeta):
    @property
    @abc.abstractmethod
    def algorithm(self) -> HashAlgorithm:
        """
        A HashAlgorithm that will be used by this context.
        """

    @abc.abstractmethod
    def update(self, data: Buffer) -> None:
        """
        Processes the provided bytes through the hash.
        """

    @abc.abstractmethod
    def finalize(self) -> bytes:
        """
        Finalizes the hash context and returns the hash digest as bytes.
        """

    @abc.abstractmethod
    def copy(self) -> HashContext:
        """
        Return a HashContext that is a copy of the current context.