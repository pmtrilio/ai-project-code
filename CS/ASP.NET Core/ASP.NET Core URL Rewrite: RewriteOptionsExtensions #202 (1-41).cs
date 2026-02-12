// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics.CodeAnalysis;
using Microsoft.AspNetCore.Http;

namespace Microsoft.AspNetCore.Rewrite;

/// <summary>
/// The builder to a list of rules for <see cref="RewriteOptions"/> and <see cref="RewriteMiddleware"/>
/// </summary>
public static class RewriteOptionsExtensions
{
    /// <summary>
    /// Adds a rule to the current rules.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="rule">A rule to be added to the current rules.</param>
    /// <returns>The Rewrite options.</returns>
    public static RewriteOptions Add(this RewriteOptions options, IRule rule)
    {
        options.Rules.Add(rule);
        return options;
    }

    /// <summary>
    /// Adds a rule to the current rules.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="applyRule">A Func that checks and applies the rule.</param>
    /// <returns></returns>
    public static RewriteOptions Add(this RewriteOptions options, Action<RewriteContext> applyRule)
    {
        options.Rules.Add(new DelegateRule(applyRule));
        return options;
    }

    /// <summary>
    /// Adds a rule that rewrites the path if the regex matches the HttpContext's PathString.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>