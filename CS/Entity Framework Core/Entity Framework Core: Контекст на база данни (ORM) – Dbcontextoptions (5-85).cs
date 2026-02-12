using Microsoft.EntityFrameworkCore.Internal;

namespace Microsoft.EntityFrameworkCore;

/// <summary>
///     The options to be used by a <see cref="DbContext" />. You normally override
///     <see cref="DbContext.OnConfiguring(DbContextOptionsBuilder)" /> or use a <see cref="DbContextOptionsBuilder" />
///     to create instances of this class and it is not designed to be directly constructed in your application code.
/// </summary>
/// <remarks>
///     See <see href="https://aka.ms/efcore-docs-dbcontext-options">Using DbContextOptions</see> for more information and examples.
/// </remarks>
public abstract class DbContextOptions : IDbContextOptions
{
    private readonly ImmutableSortedDictionary<Type, (IDbContextOptionsExtension Extension, int Ordinal)> _extensionsMap;

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    [EntityFrameworkInternal]
    protected DbContextOptions()
        => _extensionsMap = ImmutableSortedDictionary.Create<Type, (IDbContextOptionsExtension, int)>(TypeFullNameComparer.Instance);

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    [EntityFrameworkInternal]
    protected DbContextOptions(
        IReadOnlyDictionary<Type, IDbContextOptionsExtension> extensions)
        => _extensionsMap = ImmutableSortedDictionary.Create<Type, (IDbContextOptionsExtension, int)>(TypeFullNameComparer.Instance)
            .AddRange(extensions.Select((p, i) => new KeyValuePair<Type, (IDbContextOptionsExtension, int)>(p.Key, (p.Value, i))));

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    [EntityFrameworkInternal]
    protected DbContextOptions(
        ImmutableSortedDictionary<Type, (IDbContextOptionsExtension Extension, int Ordinal)> extensions)
        => _extensionsMap = extensions;

    /// <summary>
    ///     Gets the extensions that store the configured options.
    /// </summary>
    public virtual IEnumerable<IDbContextOptionsExtension> Extensions
        => _extensionsMap.Values.OrderBy(v => v.Ordinal).Select(v => v.Extension);

    /// <summary>
    ///     Gets the extension of the specified type. Returns <see langword="null" /> if no extension of the specified type is configured.
    /// </summary>
    /// <typeparam name="TExtension">The type of the extension to get.</typeparam>
    /// <returns>The extension, or <see langword="null" /> if none was found.</returns>
    public virtual TExtension? FindExtension<TExtension>()
        where TExtension : class, IDbContextOptionsExtension
        => _extensionsMap.TryGetValue(typeof(TExtension), out var value) ? (TExtension)value.Extension : null;

    /// <summary>
    ///     Gets the extension of the specified type. Throws if no extension of the specified type is configured.
    /// </summary>
    /// <typeparam name="TExtension">The type of the extension to get.</typeparam>
    /// <returns>The extension.</returns>
    public virtual TExtension GetExtension<TExtension>()
        where TExtension : class, IDbContextOptionsExtension
    {
        var extension = FindExtension<TExtension>();
        if (extension == null)
        {
            throw new InvalidOperationException(CoreStrings.OptionsExtensionNotFound(typeof(TExtension).ShortDisplayName()));
        }

        return extension;
    }
