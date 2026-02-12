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