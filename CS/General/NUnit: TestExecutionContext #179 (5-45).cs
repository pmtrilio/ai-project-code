using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Security.Principal;
using System.Threading;
using NUnit.Compatibility;
using NUnit.Framework.Constraints;
using NUnit.Framework.Interfaces;
using NUnit.Framework.Internal.Execution;
using System.Diagnostics.CodeAnalysis;

#if NETFRAMEWORK
using System.Runtime.Remoting.Messaging;
#endif

namespace NUnit.Framework.Internal
{
    /// <summary>
    /// Helper class used to save and restore certain static or
    /// singleton settings in the environment that affect tests
    /// or which might be changed by the user tests.
    /// </summary>
    public class TestExecutionContext
#if NETFRAMEWORK
        : LongLivedMarshalByRefObject, ILogicalThreadAffinative
#else
        : LongLivedMarshalByRefObject
#endif
    {
        // NOTE: Be very careful when modifying this class. It uses
        // conditional compilation extensively and you must give
        // thought to whether any new features will be supported
        // on each platform. In particular, instance fields,
        // properties, initialization and restoration must all
        // use the same conditions for each feature.

        #region Instance Fields

        /// <summary>
        /// Link to a prior saved context
        /// </summary>