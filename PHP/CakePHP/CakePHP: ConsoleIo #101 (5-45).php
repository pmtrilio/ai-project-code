 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Console;

use Cake\Console\Exception\StopException;
use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Log;
use RuntimeException;
use SplFileObject;

/**
 * A wrapper around the various IO operations shell tasks need to do.
 *
 * Packages up the stdout, stderr, and stdin streams providing a simple
 * consistent interface for shells to use. This class also makes mocking streams
 * easy to do in unit tests.
 */
class ConsoleIo
{
    /**
     * Output constant making verbose shells.
     *
     * @var int
     */
    public const VERBOSE = 2;

    /**
     * Output constant for making normal shells.
     *
     * @var int
     */