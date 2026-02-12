using System.Collections.Generic;
using System.ComponentModel;
using System.Diagnostics;
using System.Globalization;
using System.Linq;
using System.Linq.Expressions;
using System.Reflection;
using System.Threading.Tasks;
using Moq;
using Moq.Async;
using Moq.Properties;

namespace Moq
{
    /// <summary>
    ///   Base class for mocks and static helper class with methods that apply to mocked objects,
    ///   such as <see cref="Get"/> to retrieve a <see cref="Mock{T}"/> from an object instance.
    /// </summary>
    public abstract partial class Mock : IFluentInterface
    {
        internal static readonly MethodInfo GetMethod =
            typeof(Mock).GetMethod(nameof(Get), BindingFlags.Public | BindingFlags.Static);

        /// <summary>
        ///   Initializes a new instance of the <see cref="Mock"/> class.
        /// </summary>
        protected Mock()
        {
        }

        /// <summary>
        ///   Retrieves the mock object for the given object instance.
        /// </summary>
        /// <param name="mocked">The instance of the mocked object.</param>
        /// <typeparam name="T">
        ///   Type of the mock to retrieve.
        ///   Can be omitted as it's inferred from the object instance passed in as the <paramref name="mocked"/> instance.
        /// </typeparam>
        /// <returns>The mock associated with the mocked object.</returns>
        /// <exception cref="ArgumentException">The received <paramref name="mocked"/> instance was not created by Moq.</exception>
        /// <example group="advanced">