 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Factory;

use RuntimeException;
use Slim\Factory\Psr17\Psr17Factory;
use Slim\Factory\Psr17\Psr17FactoryProvider;
use Slim\Factory\Psr17\SlimHttpServerRequestCreator;
use Slim\Interfaces\Psr17FactoryProviderInterface;
use Slim\Interfaces\ServerRequestCreatorInterface;

/** @api */
class ServerRequestCreatorFactory
{
    protected static ?Psr17FactoryProviderInterface $psr17FactoryProvider = null;

    protected static ?ServerRequestCreatorInterface $serverRequestCreator = null;

    protected static bool $slimHttpDecoratorsAutomaticDetectionEnabled = true;

    public static function create(): ServerRequestCreatorInterface
    {
        return static::determineServerRequestCreator();
    }

    /**
     * @throws RuntimeException
     */
    public static function determineServerRequestCreator(): ServerRequestCreatorInterface
    {
        if (static::$serverRequestCreator) {
            return static::attemptServerRequestCreatorDecoration(static::$serverRequestCreator);
        }

        $psr17FactoryProvider = static::$psr17FactoryProvider ?? new Psr17FactoryProvider();

        /** @var Psr17Factory $psr17Factory */