<?php

namespace Illuminate\Validation;

use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ValidatedInput;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Validator implements ValidatorContract
{
    use Concerns\FormatsMessages,
        Concerns\ValidatesAttributes;

    /**
     * The Translator implementation.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface
     */
    protected $presenceVerifier;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * Attributes that should be excluded from the validated data.
     *
     * @var array
     */
    protected $excludeAttributes = [];

    /**
     * The message bag instance.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The initial rules provided.
     *
     * @var array
     */
    protected $initialRules;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The current rule that is validating.
     *
     * @var string
     */
    protected $currentRule;

    /**
     * The array of wildcard attributes with their asterisks expanded.
     *