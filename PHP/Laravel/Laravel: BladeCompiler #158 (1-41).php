<?php

namespace Illuminate\View\Compilers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\View\Component;
use InvalidArgumentException;

class BladeCompiler extends Compiler implements CompilerInterface
{
    use Concerns\CompilesAuthorizations,
        Concerns\CompilesClasses,
        Concerns\CompilesComments,
        Concerns\CompilesComponents,
        Concerns\CompilesConditionals,
        Concerns\CompilesEchos,
        Concerns\CompilesErrors,
        Concerns\CompilesFragments,
        Concerns\CompilesHelpers,
        Concerns\CompilesIncludes,
        Concerns\CompilesInjections,
        Concerns\CompilesJson,
        Concerns\CompilesJs,
        Concerns\CompilesLayouts,
        Concerns\CompilesLoops,
        Concerns\CompilesRawPhp,
        Concerns\CompilesSessions,
        Concerns\CompilesStacks,
        Concerns\CompilesStyles,
        Concerns\CompilesTranslations,
        Concerns\CompilesUseStatements,
        ReflectsClosures;
