// Copyright 2013-2016 Serilog Contributors
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

using System.Diagnostics;

#pragma warning disable Serilog004 // Constant MessageTemplate verifier

namespace Serilog.Core;

/// <summary>
/// The core Serilog logging pipeline. A <see cref="Logger"/> must
/// be disposed to flush any events buffered within it. Most application
/// code should depend on <see cref="ILogger"/>, not this class.
/// </summary>
public sealed class Logger : ILogger, ILogEventSink, IDisposable
#if FEATURE_ASYNCDISPOSABLE
    , IAsyncDisposable
#endif
{
    static readonly object[] NoPropertyValues = Array.Empty<object>();
    static readonly LogEventProperty[] NoProperties = Array.Empty<LogEventProperty>();

    readonly MessageTemplateProcessor _messageTemplateProcessor;
    readonly ILogEventSink _sink;
    readonly Action? _dispose;
#if FEATURE_ASYNCDISPOSABLE
    readonly Func<ValueTask>? _disposeAsync;
#endif
    readonly ILogEventEnricher _enricher;

    // It's important that checking minimum level is a very
    // quick (CPU-cacheable) read in the simple case, hence
    // we keep a separate field from the switch, which may
    // not be specified. If it is, we'll set _minimumLevel
    // to its lower limit and fall through to the secondary check.
    readonly LogEventLevel _minimumLevel;
    readonly LoggingLevelSwitch? _levelSwitch;
    readonly LevelOverrideMap? _overrideMap;

    internal Logger(
        MessageTemplateProcessor messageTemplateProcessor,
        LogEventLevel minimumLevel,
        LoggingLevelSwitch? levelSwitch,
        ILogEventSink sink,
        ILogEventEnricher enricher,
        Action? dispose,
#if FEATURE_ASYNCDISPOSABLE
        Func<ValueTask>? disposeAsync,
#endif
        LevelOverrideMap? overrideMap)