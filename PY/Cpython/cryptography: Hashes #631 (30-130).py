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
        """


Hash = rust_openssl.hashes.Hash
HashContext.register(Hash)

XOFHash = rust_openssl.hashes.XOFHash


class ExtendableOutputFunction(metaclass=abc.ABCMeta):
    """
    An interface for extendable output functions.
    """


class SHA1(HashAlgorithm):
    name = "sha1"
    digest_size = 20
    block_size = 64


class SHA512_224(HashAlgorithm):  # noqa: N801
    name = "sha512-224"
    digest_size = 28
    block_size = 128


class SHA512_256(HashAlgorithm):  # noqa: N801
    name = "sha512-256"
    digest_size = 32
    block_size = 128


class SHA224(HashAlgorithm):
    name = "sha224"
    digest_size = 28
    block_size = 64


class SHA256(HashAlgorithm):
    name = "sha256"
    digest_size = 32
    block_size = 64

