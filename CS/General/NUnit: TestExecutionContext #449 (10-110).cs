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
        private readonly TestExecutionContext? _priorContext;

        /// <summary>
        /// Indicates that a stop has been requested
        /// </summary>
        private TestExecutionStatus _executionStatus;

        /// <summary>
        /// The event listener currently receiving notifications
        /// </summary>
        private ITestListener _listener = TestListener.NULL;

        /// <summary>
        /// The number of assertions for the current test
        /// </summary>
        private int _assertCount;

        private Randomizer? _randomGenerator;

        /// <summary>
        /// The current test result
        /// </summary>
        private TestResult _currentResult;

        private SandboxedThreadState _sandboxedThreadState;

        private ExecutionHooks.ExecutionHooks? _executionHooks;

        #endregion

        #region Constructors

        // TODO: Fix design where properties are not set at unknown times.

#pragma warning disable CS8618 // Non-nullable field must contain a non-null value when exiting constructor. Consider declaring as nullable.

        /// <summary>
        /// Initializes a new instance of the <see cref="TestExecutionContext"/> class.
        /// </summary>
        public TestExecutionContext()
        {
            _priorContext = null;
            TestCaseTimeout = 0;
            UpstreamActions = new List<ITestAction>();

            UpdateContextFromEnvironment();

            CurrentValueFormatter = (val) => MsgUtils.DefaultValueFormatter(val);
            IsSingleThreaded = false;
            DefaultFloatingPointTolerance = Tolerance.Default;
        }

        /// <summary>
        /// Initializes a new instance of the <see cref="TestExecutionContext"/> class.
        /// </summary>
        /// <param name="other">An existing instance of TestExecutionContext.</param>
        public TestExecutionContext(TestExecutionContext other)
        {
            _priorContext = other;

            CurrentTest = other.CurrentTest;
            if (other._executionHooks is not null)
            {
                _executionHooks = new ExecutionHooks.ExecutionHooks(other._executionHooks);
            }