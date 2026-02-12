// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.ComponentModel;
using System.Runtime.CompilerServices;
using Microsoft.EntityFrameworkCore.ChangeTracking.Internal;
using Microsoft.EntityFrameworkCore.Internal;
using Microsoft.EntityFrameworkCore.Metadata.Internal;

namespace Microsoft.EntityFrameworkCore;

/// <summary>
///     A DbContext instance represents a session with the database and can be used to query and save
///     instances of your entities. DbContext is a combination of the Unit Of Work and Repository patterns.
/// </summary>
/// <remarks>
///     <para>
///         Entity Framework Core does not support multiple parallel operations being run on the same DbContext instance. This
///         includes both parallel execution of async queries and any explicit concurrent use from multiple threads.
///         Therefore, always await async calls immediately, or use separate DbContext instances for operations that execute
///         in parallel. See <see href="https://aka.ms/efcore-docs-threading">Avoiding DbContext threading issues</see> for more information
///         and examples.
///     </para>
///     <para>
///         Typically you create a class that derives from DbContext and contains <see cref="DbSet{TEntity}" />
///         properties for each entity in the model. If the <see cref="DbSet{TEntity}" /> properties have a public setter,
///         they are automatically initialized when the instance of the derived context is created.
///     </para>
///     <para>
///         Override the <see cref="OnConfiguring(DbContextOptionsBuilder)" /> method to configure the database (and
///         other options) to be used for the context. Alternatively, if you would rather perform configuration externally
///         instead of inline in your context, you can use <see cref="DbContextOptionsBuilder{TContext}" />
///         (or <see cref="DbContextOptionsBuilder" />) to externally create an instance of <see cref="DbContextOptions{TContext}" />
///         (or <see cref="DbContextOptions" />) and pass it to a base constructor of <see cref="DbContext" />.
///     </para>
///     <para>
///         The model is discovered by running a set of conventions over the entity classes found in the
///         <see cref="DbSet{TEntity}" /> properties on the derived context. To further configure the model that
///         is discovered by convention, you can override the <see cref="OnModelCreating(ModelBuilder)" /> method.
///     </para>
///     <para>