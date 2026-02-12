// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Text.RegularExpressions;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Extensions;
using Microsoft.AspNetCore.Rewrite.Logging;

namespace Microsoft.AspNetCore.Rewrite;

internal sealed class RedirectRule : IRule
{
    private readonly TimeSpan _regexTimeout = TimeSpan.FromSeconds(1);
    public Regex InitialMatch { get; }
    public string Replacement { get; }
    public int StatusCode { get; }
    public RedirectRule(string regex, string replacement, int statusCode)
    {
        ArgumentException.ThrowIfNullOrEmpty(regex);
        ArgumentException.ThrowIfNullOrEmpty(replacement);

        InitialMatch = new Regex(regex, RegexOptions.Compiled | RegexOptions.CultureInvariant, _regexTimeout);
        Replacement = replacement;
        StatusCode = statusCode;
    }

    public void ApplyRule(RewriteContext context)
    {
        var request = context.HttpContext.Request;
        var path = request.Path;
        var pathBase = request.PathBase;

        Match initMatchResults;
        if (!path.HasValue)
        {
            initMatchResults = InitialMatch.Match(string.Empty);
        }
        else
        {
            initMatchResults = InitialMatch.Match(path.ToString().Substring(1));
        }