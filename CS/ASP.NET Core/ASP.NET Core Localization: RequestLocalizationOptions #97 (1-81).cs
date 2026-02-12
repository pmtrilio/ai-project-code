// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Globalization;
using Microsoft.AspNetCore.Localization;

namespace Microsoft.AspNetCore.Builder;

/// <summary>
/// Specifies options for the <see cref="RequestLocalizationMiddleware"/>.
/// </summary>
public class RequestLocalizationOptions
{
    private RequestCulture _defaultRequestCulture =
        new RequestCulture(CultureInfo.CurrentCulture, CultureInfo.CurrentUICulture);

    /// <summary>
    /// Creates a new <see cref="RequestLocalizationOptions"/> with default values.
    /// </summary>
    public RequestLocalizationOptions()
    {
        RequestCultureProviders = new List<IRequestCultureProvider>
        {
            new QueryStringRequestCultureProvider { Options = this },
            new CookieRequestCultureProvider { Options = this },
            new AcceptLanguageHeaderRequestCultureProvider { Options = this }
        };
    }

    /// <summary>
    /// Configures <see cref="CultureInfo.UseUserOverride "/>. Defaults to <c>true</c>.
    /// </summary>
    public bool CultureInfoUseUserOverride { get; set; } = true;

    /// <summary>
    /// Gets or sets the default culture to use for requests when a supported culture could not be determined by
    /// one of the configured <see cref="IRequestCultureProvider"/>s.
    /// Defaults to <see cref="CultureInfo.CurrentCulture"/> and <see cref="CultureInfo.CurrentUICulture"/>.
    /// </summary>
    public RequestCulture DefaultRequestCulture
    {
        get
        {
            return _defaultRequestCulture;
        }
        set
        {
            ArgumentNullException.ThrowIfNull(value);

            _defaultRequestCulture = value;
        }
    }

    /// <summary>
    /// Gets or sets a value indicating whether to set a request culture to an parent culture in the case the
    /// culture determined by the configured <see cref="IRequestCultureProvider"/>s is not in the
    /// <see cref="SupportedCultures"/> list but a parent culture is.
    /// Defaults to <c>true</c>;
    /// </summary>
    /// <remarks>
    /// Note that the parent culture check is done using only the culture name.
    /// </remarks>
    /// <example>
    /// If this property is <c>true</c> and the application is configured to support the culture "fr", but not the
    /// culture "fr-FR", and a configured <see cref="IRequestCultureProvider"/> determines a request's culture is
    /// "fr-FR", then the request's culture will be set to the culture "fr", as it is a parent of "fr-FR".
    /// </example>
    public bool FallBackToParentCultures { get; set; } = true;

    /// <summary>
    /// Gets or sets a value indicating whether to set a request UI culture to a parent culture in the case the
    /// UI culture determined by the configured <see cref="IRequestCultureProvider"/>s is not in the
    /// <see cref="SupportedUICultures"/> list but a parent culture is.
    /// Defaults to <c>true</c>;
    /// </summary>
    /// <remarks>
    /// Note that the parent culture check is done using ony the culture name.
    /// </remarks>
    /// <example>
    /// If this property is <c>true</c> and the application is configured to support the UI culture "fr", but not
    /// the UI culture "fr-FR", and a configured <see cref="IRequestCultureProvider"/> determines a request's UI