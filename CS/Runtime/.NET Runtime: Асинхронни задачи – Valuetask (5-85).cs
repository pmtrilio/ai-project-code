using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Threading.Tasks.Sources;

namespace System.Threading.Tasks
{
    // TYPE SAFETY WARNING:
    // This code uses Unsafe.As to cast _obj.  This is done in order to minimize the costs associated with
    // casting _obj to a variety of different types that can be stored in a ValueTask, e.g. Task<TResult>
    // vs IValueTaskSource<TResult>.  Previous attempts at this were faulty due to using a separate field
    // to store information about the type of the object in _obj; this is faulty because if the ValueTask
    // is stored into a field, concurrent read/writes can result in tearing the _obj from the type information
    // stored in a separate field.  This means we can rely only on the _obj field to determine how to handle
    // it.  As such, the pattern employed is to copy _obj into a local obj, and then check it for null and
    // type test against Task/Task<TResult>.  Since the ValueTask can only be constructed with null, Task,
    // or IValueTaskSource, we can then be confident in knowing that if it doesn't match one of those values,
    // it must be an IValueTaskSource, and we can use Unsafe.As.  This could be defeated by other unsafe means,
    // like private reflection or using Unsafe.As manually, but at that point you're already doing things
    // that can violate type safety; we only care about getting correct behaviors when using "safe" code.
    // There are still other race conditions in user's code that can result in errors, but such errors don't
    // cause ValueTask to violate type safety.

    /// <summary>Provides an awaitable result of an asynchronous operation.</summary>
    /// <remarks>
    /// <see cref="ValueTask"/> instances are meant to be directly awaited.  To do more complicated operations with them, a <see cref="Task"/>
    /// should be extracted using <see cref="AsTask"/>.  Such operations might include caching a task instance to be awaited later,
    /// registering multiple continuations with a single task, awaiting the same task multiple times, and using combinators over
    /// multiple operations:
    /// <list type="bullet">
    /// <item>
    /// Once the result of a <see cref="ValueTask"/> instance has been retrieved, do not attempt to retrieve it again.
    /// <see cref="ValueTask"/> instances may be backed by <see cref="IValueTaskSource"/> instances that are reusable, and such
    /// instances may use the act of retrieving the instances result as a notification that the instance may now be reused for
    /// a different operation.  Attempting to then reuse that same <see cref="ValueTask"/> results in undefined behavior.
    /// </item>
    /// <item>
    /// Do not attempt to add multiple continuations to the same <see cref="ValueTask"/>.  While this might work if the
    /// <see cref="ValueTask"/> wraps a <code>T</code> or a <see cref="Task"/>, it may not work if the <see cref="ValueTask"/>
    /// was constructed from an <see cref="IValueTaskSource"/>.
    /// </item>
    /// <item>
    /// Some operations that return a <see cref="ValueTask"/> may invalidate it based on some subsequent operation being performed.
    /// Unless otherwise documented, assume that a <see cref="ValueTask"/> should be awaited prior to performing any additional operations
    /// on the instance from which it was retrieved.
    /// </item>
    /// </list>
    /// </remarks>
    [AsyncMethodBuilder(typeof(AsyncValueTaskMethodBuilder))]
    [StructLayout(LayoutKind.Auto)]
    public readonly struct ValueTask : IEquatable<ValueTask>
    {
        /// <summary>A task canceled using `new CancellationToken(true)`. Lazily created only when first needed.</summary>
        private static volatile Task? s_canceledTask;

        /// <summary>null if representing a successful synchronous completion, otherwise a <see cref="Task"/> or a <see cref="IValueTaskSource"/>.</summary>
        internal readonly object? _obj;
        /// <summary>Opaque value passed through to the <see cref="IValueTaskSource"/>.</summary>
        internal readonly short _token;
        /// <summary>true to continue on the captured context; otherwise, false.</summary>
        /// <remarks>Stored in the <see cref="ValueTask"/> rather than in the configured awaiter to utilize otherwise padding space.</remarks>
        internal readonly bool _continueOnCapturedContext;

        // An instance created with the default ctor (a zero init'd struct) represents a synchronously, successfully completed operation.

        /// <summary>Initialize the <see cref="ValueTask"/> with a <see cref="Task"/> that represents the operation.</summary>
        /// <param name="task">The task.</param>
        [MethodImpl(MethodImplOptions.AggressiveInlining)]
        public ValueTask(Task task)
        {
            if (task == null)
            {
                ThrowHelper.ThrowArgumentNullException(ExceptionArgument.task);
            }

            _obj = task;

            _continueOnCapturedContext = true;
            _token = 0;
        }