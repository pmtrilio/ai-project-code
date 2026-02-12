using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using NuGet.Common;
using NuGet.Frameworks;
using NuGet.Packaging.Core;
using NuGet.Packaging.Signing;
using NuGet.Shared;
using NuGet.Versioning;

namespace NuGet.Packaging
{
    /// <summary>
    /// Abstract class that both the zip and folder package readers extend
    /// This class contains the path conventions for both zip and folder readers
    /// </summary>
    public abstract class PackageReaderBase : IPackageCoreReader, IPackageContentReader, IAsyncPackageCoreReader, IAsyncPackageContentReader, ISignedPackageReader
    {
        private NuspecReader _nuspecReader;

        protected IFrameworkNameProvider FrameworkProvider { get; set; }
        protected IFrameworkCompatibilityProvider CompatibilityProvider { get; set; }

        /// <summary>
        /// Instantiates a new <see cref="PackageReaderBase" /> class.
        /// </summary>
        /// <param name="frameworkProvider">A framework mapping provider.</param>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="frameworkProvider" /> is <see langword="null" />.</exception>
        public PackageReaderBase(IFrameworkNameProvider frameworkProvider)
            : this(frameworkProvider, new CompatibilityProvider(frameworkProvider))
        {
        }

        /// <summary>
        /// Instantiates a new <see cref="PackageReaderBase" /> class.
        /// </summary>
        /// <param name="frameworkProvider">A framework mapping provider.</param>
        /// <param name="compatibilityProvider">A framework compatibility provider.</param>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="frameworkProvider" /> is <see langword="null" />.</exception>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="compatibilityProvider" /> is <see langword="null" />.</exception>
        public PackageReaderBase(IFrameworkNameProvider frameworkProvider, IFrameworkCompatibilityProvider compatibilityProvider)