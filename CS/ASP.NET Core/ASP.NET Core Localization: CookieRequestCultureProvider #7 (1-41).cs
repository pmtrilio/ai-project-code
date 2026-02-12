// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using Microsoft.AspNetCore.Http;

namespace Microsoft.AspNetCore.Localization;

/// <summary>
/// Determines the culture information for a request via the value of a cookie.
/// </summary>
public class CookieRequestCultureProvider : RequestCultureProvider
{
    private const char _cookieSeparator = '|';
    private const string _culturePrefix = "c=";
    private const string _uiCulturePrefix = "uic=";

    /// <summary>
    /// Represent the default cookie name used to track the user's preferred culture information, which is ".AspNetCore.Culture".
    /// </summary>
    public static readonly string DefaultCookieName = ".AspNetCore.Culture";

    /// <summary>
    /// The name of the cookie that contains the user's preferred culture information.
    /// Defaults to <see cref="DefaultCookieName"/>.
    /// </summary>
    public string CookieName { get; set; } = DefaultCookieName;

    /// <inheritdoc />
    public override Task<ProviderCultureResult?> DetermineProviderCultureResult(HttpContext httpContext)
    {
        ArgumentNullException.ThrowIfNull(httpContext);

        var cookie = httpContext.Request.Cookies[CookieName];

        if (string.IsNullOrEmpty(cookie))
        {
            return NullProviderCultureResult;
        }

        var providerResultCulture = ParseCookieValue(cookie);
