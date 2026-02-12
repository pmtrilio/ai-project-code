<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\log;

use yii\base\Component;

/**
 * Logger records logged messages in memory and sends them to different targets if [[dispatcher]] is set.
 *
 * A Logger instance can be accessed via `Yii::getLogger()`. You can call the method [[log()]] to record a single log message.
 * For convenience, a set of shortcut methods are provided for logging messages of various severity levels
 * via the [[Yii]] class:
 *
 * - [[Yii::trace()]]
 * - [[Yii::error()]]
 * - [[Yii::warning()]]
 * - [[Yii::info()]]
 * - [[Yii::beginProfile()]]
 * - [[Yii::endProfile()]]
 *
 * For more details and usage information on Logger, see the [guide article on logging](guide:runtime-logging).
 *
 * When the application ends or [[flushInterval]] is reached, Logger will call [[flush()]]
 * to send logged messages to different log targets, such as [[FileTarget|file]], [[EmailTarget|email]],
 * or [[DbTarget|database]], with the help of the [[dispatcher]].
 *
 * @property-read array $dbProfiling The first element indicates the number of SQL statements executed, and
 * the second element the total time spent in SQL execution.
 * @property-read float $elapsedTime The total elapsed time in seconds for current request.
 * @property-read array $profiling The profiling results. Each element is an array consisting of these
 * elements: `info`, `category`, `timestamp`, `trace`, `level`, `duration`, `memory`, `memoryDiff`. The `memory`
 * and `memoryDiff` values are available since version 2.0.11.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Logger extends Component
{
    /**
     * Error message level. An error message is one that indicates the abnormal termination of the
     * application and may require developer's handling.
     */
    public const LEVEL_ERROR = 0x01;
    /**
     * Warning message level. A warning message is one that indicates some abnormal happens but
     * the application is able to continue to run. Developers should pay attention to this message.
     */
    public const LEVEL_WARNING = 0x02;
    /**
     * Informational message level. An informational message is one that includes certain information
     * for developers to review.
     */
    public const LEVEL_INFO = 0x04;
    /**
     * Tracing message level. A tracing message is one that reveals the code execution flow.
     */
    public const LEVEL_TRACE = 0x08;
    /**
     * Profiling message level. This indicates the message is for profiling purpose.
     */
    public const LEVEL_PROFILE = 0x40;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the beginning
     * of a profiling block.
     */
    public const LEVEL_PROFILE_BEGIN = 0x50;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the end
     * of a profiling block.
     */
    public const LEVEL_PROFILE_END = 0x60;
    /**
     * @var array logged messages. This property is managed by [[log()]] and [[flush()]].
     * Each log message is of the following structure:
     *