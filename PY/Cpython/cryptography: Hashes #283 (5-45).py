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
