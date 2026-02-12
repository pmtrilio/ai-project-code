<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use function array_diff;
use function basename;
use function get_declared_classes;
use function realpath;
use function str_ends_with;
use function strpos;
use function strtolower;
use function substr;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestSuiteLoader
{
    /**
     * @var list<class-string>
     */
    private static array $declaredClasses = [];

    /**
     * @var array<non-empty-string, list<class-string>>
     */
    private static array $fileToClassesMap = [];

    /**
     * @throws Exception
     *
     * @return ReflectionClass<TestCase>
     */
    public function load(string $suiteClassFile): ReflectionClass
    {
        $suiteClassFile = realpath($suiteClassFile);
        $suiteClassName = $this->classNameFromFileName($suiteClassFile);
        $loadedClasses  = $this->loadSuiteClassFile($suiteClassFile);

        foreach ($loadedClasses as $className) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $class = new ReflectionClass($className);

            if ($class->isAnonymous()) {
                continue;
            }

            if ($class->getFileName() !== $suiteClassFile) {
                continue;
            }