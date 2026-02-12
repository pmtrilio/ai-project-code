 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Validation;

use ArrayAccess;
use ArrayIterator;
use BackedEnum;
use Closure;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Psr\Http\Message\UploadedFileInterface;
use Traversable;
use function Cake\I18n\__d;

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