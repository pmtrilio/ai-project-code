<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         5.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\I18n;

use Cake\Chronos\ChronosTime;
use Closure;
use IntlDateFormatter;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Extends time class provided by Chronos.
 *
 * Adds handy methods and locale-aware formatting helpers.
 *
 * @phpstan-immutable
 */
class Time extends ChronosTime implements JsonSerializable, Stringable
{
    use DateFormatTrait;

    /**
     * The format to use when formatting a time using `Cake\I18n\Time::i18nFormat()`
     * and `__toString`.
     *
     * The format should be either the formatting constants from IntlDateFormatter as