/**
 * Validator object encapsulates all methods related to data validations for a model
 * It also provides an API to dynamically change validation rules for each model field.
 *
 * Implements ArrayAccess to easily modify rules in the set
 *
 * @link https://book.cakephp.org/5/en/core-libraries/validation.html
 * @template-implements \ArrayAccess<string, \Cake\Validation\ValidationSet>
 * @template-implements \IteratorAggregate<string, \Cake\Validation\ValidationSet>
 */
class Validator implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * By using 'create' you can make fields required when records are first created.
     *
     * @var string
     */
    public const WHEN_CREATE = 'create';

    /**
     * By using 'update', you can make fields required when they are updated.
     *
     * @var string
     */
    public const WHEN_UPDATE = 'update';

    /**
     * Used to flag nested rules created with addNested() and addNestedMany()
     *
     * @var string
     */
    public const NESTED = '_nested';

    /**
     * A flag for allowEmptyFor()
     *
     * When `null` is given, it will be recognized as empty.
     *
     * @var int
     */
    public const EMPTY_NULL = 0;

    /**
     * A flag for allowEmptyFor()
     *
     * When an empty string is given, it will be recognized as empty.
     *
     * @var int
     */
    public const EMPTY_STRING = 1;

    /**
     * A flag for allowEmptyFor()
     *
     * When an empty array is given, it will be recognized as empty.
     *
     * @var int
     */
    public const EMPTY_ARRAY = 2;

    /**
     * A flag for allowEmptyFor()
     *
     * The return value of \Psr\Http\Message\UploadedFileInterface::getError()
     * method must be equal to `UPLOAD_ERR_NO_FILE`.
     *
     * @var int
     */
    public const EMPTY_FILE = 4;

    /**
     * A flag for allowEmptyFor()
     *
     * When an array is given, if it contains the `year` key, and only empty strings
     * or null values, it will be recognized as empty.
     *
     * @var int
     */
    public const EMPTY_DATE = 8;

    /**
     * A flag for allowEmptyFor()
     *
     * When an array is given, if it contains the `hour` key, and only empty strings
     * or null values, it will be recognized as empty.
     *
     * @var int
     */
    public const EMPTY_TIME = 16;

    /**
     * A combination of the all EMPTY_* flags
     *
     * @var int
     */
    public const EMPTY_ALL = self::EMPTY_STRING
        | self::EMPTY_ARRAY
        | self::EMPTY_FILE
        | self::EMPTY_DATE
        | self::EMPTY_TIME;
