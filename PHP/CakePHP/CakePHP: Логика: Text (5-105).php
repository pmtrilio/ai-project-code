 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Utility;

use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Closure;
use InvalidArgumentException;
use Transliterator;
use function Cake\I18n\__d;

/**
 * Text handling methods.
 */
class Text
{
    /**
     * Default transliterator.
     *
     * @var \Transliterator|null Transliterator instance.
     */
    protected static ?Transliterator $_defaultTransliterator = null;

    /**
     * Default transliterator id string.
     *
     * @var string Transliterator identifier string.
     */
    protected static string $_defaultTransliteratorId = 'Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove';

    /**
     * Default HTML tags which must not be counted for truncating text.
     *
     * @var array<string>
     */
    protected static array $_defaultHtmlNoCount = [
        'style',
        'script',
    ];

    /**
     * Whether to use I18n functions for translating default error messages
     *
     * @var bool
     */
    protected static bool $useI18n;

    /**
     * Generate a random UUID version 4
     *
     * Warning: This method should not be used as a random seed for any cryptographic operations.
     * Instead, you should use `Security::randomBytes()` or `Security::randomString()` instead.
     *
     * It should also not be used to create identifiers that have security implications, such as
     * 'unguessable' URL identifiers. Instead, you should use {@link \Cake\Utility\Security::randomBytes()}` for that.
     *
     * ### Custom UUID generation
     *
     * You can configure a custom UUID generator by setting a Closure via Configure:
     *
     * ```
     * Configure::write('Text.uuidGenerator', function () {
     *     // Return your custom UUID string
     *     return MyUuidLibrary::generate();
     * });
     * ```
     *
     * @see https://www.ietf.org/rfc/rfc4122.txt
     * @return string RFC 4122 UUID
     * @copyright Matt Farina MIT License https://github.com/lootils/uuid/blob/master/LICENSE
     */
    public static function uuid(): string
    {
        $generator = Configure::read('Text.uuidGenerator');
        if ($generator instanceof Closure) {
            return $generator();
        }

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            random_int(0, 65535),
            random_int(0, 65535),
            // 16 bits for "time_mid"
            random_int(0, 65535),
            // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
            random_int(0, 4095) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,