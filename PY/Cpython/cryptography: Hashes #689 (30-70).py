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