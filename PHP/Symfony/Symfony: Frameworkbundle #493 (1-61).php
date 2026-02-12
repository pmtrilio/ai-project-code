<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddAnnotationsCachedReaderPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddDebugLogProcessorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AssetsContextPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ContainerBuilderDebugDumpPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ErrorLoggerCompilerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ProfilerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\RemoveUnusedSessionMarshallingHandlerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerRealRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerWeakRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TranslationUpdateCommandPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\UnusedTagsPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\VirtualRequestStackPass;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\DependencyInjection\CacheCollectorPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolClearerPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPrunerPass;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\RegisterReverseContainerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Form\DependencyInjection\FormPass;
use Symfony\Component\HttpClient\DependencyInjection\HttpClientPass;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;
use Symfony\Component\HttpKernel\DependencyInjection\LoggerPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\ResettableServicePass;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;
use Symfony\Component\Mime\DependencyInjection\AddMimeTypeGuesserPass;
use Symfony\Component\PropertyInfo\DependencyInjection\PropertyInfoPass;
use Symfony\Component\Routing\DependencyInjection\AddExpressionLanguageProvidersPass;
use Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;