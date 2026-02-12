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
 * @since         2.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Utility;

use ArrayAccess;
use Cake\Core\Exception\CakeException;
use InvalidArgumentException;
use const SORT_ASC;
use const SORT_DESC;
use const SORT_LOCALE_STRING;
use const SORT_NATURAL;
use const SORT_NUMERIC;
use const SORT_REGULAR;
use const SORT_STRING;

/**
 * Library of array functions for manipulating and extracting data
 * from arrays or 'sets' of data.
 *
 * `Hash` provides an improved interface, more consistent and
 * predictable set of features over `Set`. While it lacks the spotty
 * support for pseudo Xpath, its more fully featured dot notation provides
 * similar features in a more consistent implementation.
 *
 * @link https://book.cakephp.org/5/en/core-libraries/hash.html
 */
class Hash
{
    /**
     * Get a single value specified by $path out of $data.
     * Does not support the full dot notation feature set,
     * but is faster for simple read operations.
     *
     * @param \ArrayAccess|array $data Array of data or object implementing
     *   \ArrayAccess interface to operate on.
     * @param array<string>|string|int|null $path The path being searched for. Either a dot
     *   separated string, or an array of path segments. If null, returns $default.
     * @param mixed $default The return value when the path does not exist or is null.
     * @throws \InvalidArgumentException
     * @return mixed The value fetched from the array, or $default if path doesn't exist, is null,
     *   or $data is empty.
     * @link https://book.cakephp.org/5/en/core-libraries/hash.html#Cake\Utility\Hash::get
     */
    public static function get(ArrayAccess|array $data, array|string|int|null $path, mixed $default = null): mixed
    {
        if (!$data || $path === null) {
            return $default;
        }

        if (is_string($path) || is_int($path)) {
            $parts = explode('.', (string)$path);
        } else {
            $parts = $path;
        }

        switch (count($parts)) {
            case 1:
                return $data[$parts[0]] ?? $default;
            case 2:
                return $data[$parts[0]][$parts[1]] ?? $default;
            case 3:
                return $data[$parts[0]][$parts[1]][$parts[2]] ?? $default;
            default:
                foreach ($parts as $key) {
                    if ((is_array($data) || $data instanceof ArrayAccess) && isset($data[$key])) {
                        $data = $data[$key];
                    } else {
                        return $default;