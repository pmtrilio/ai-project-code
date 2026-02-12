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
    /// <param name="regex">The regex string to compare with.</param>
    /// <param name="replacement">If the regex matches, what to replace the uri with.</param>
    /// <param name="skipRemainingRules">If the regex matches, conditionally stop processing other rules.</param>
    /// <returns>The Rewrite options.</returns>
    public static RewriteOptions AddRewrite(this RewriteOptions options, [StringSyntax(StringSyntaxAttribute.Regex)] string regex, string replacement, bool skipRemainingRules)
    {
        options.Rules.Add(new RewriteRule(regex, replacement, skipRemainingRules));
        return options;
    }

    /// <summary>
    /// Redirect the request if the regex matches the HttpContext's PathString, with returning a 302
    /// status code for found.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="regex">The regex string to compare with.</param>
    /// <param name="replacement">If the regex matches, what to replace the uri with.</param>
    /// <returns>The Rewrite options.</returns>
    public static RewriteOptions AddRedirect(this RewriteOptions options, [StringSyntax(StringSyntaxAttribute.Regex)] string regex, string replacement)
    {
        return AddRedirect(options, regex, replacement, statusCode: StatusCodes.Status302Found);
    }

    /// <summary>
    /// Redirect the request if the regex matches the HttpContext's PathString
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="regex">The regex string to compare with.</param>
    /// <param name="replacement">If the regex matches, what to replace the uri with.</param>
    /// <param name="statusCode">The status code to add to the response.</param>
    /// <returns>The Rewrite options.</returns>
    public static RewriteOptions AddRedirect(this RewriteOptions options, [StringSyntax(StringSyntaxAttribute.Regex)] string regex, string replacement, int statusCode)
    {
        options.Rules.Add(new RedirectRule(regex, replacement, statusCode));
        return options;
    }

    /// <summary>
    /// Redirect a request to https if the incoming request is http, with returning a 301
    /// status code for permanently redirected.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <returns></returns>
    public static RewriteOptions AddRedirectToHttpsPermanent(this RewriteOptions options)
    {
        return AddRedirectToHttps(options, statusCode: StatusCodes.Status301MovedPermanently, sslPort: null);
    }

    /// <summary>
    /// Redirect a request to https if the incoming request is http
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    public static RewriteOptions AddRedirectToHttps(this RewriteOptions options)
    {
        return AddRedirectToHttps(options, statusCode: StatusCodes.Status302Found, sslPort: null);
    }

    /// <summary>
    /// Redirect a request to https if the incoming request is http
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="statusCode">The status code to add to the response.</param>
    public static RewriteOptions AddRedirectToHttps(this RewriteOptions options, int statusCode)
    {
        return AddRedirectToHttps(options, statusCode, sslPort: null);
    }

    /// <summary>
    /// Redirect a request to https if the incoming request is http
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <param name="statusCode">The status code to add to the response.</param>
    /// <param name="sslPort">The SSL port to add to the response.</param>
    public static RewriteOptions AddRedirectToHttps(this RewriteOptions options, int statusCode, int? sslPort)
    {
        options.Rules.Add(new RedirectToHttpsRule { StatusCode = statusCode, SSLPort = sslPort });
        return options;
    }

    /// <summary>
    /// Permanently redirects the request to the www subdomain if the request is non-www.
    /// </summary>
    /// <param name="options">The <see cref="RewriteOptions"/>.</param>
    /// <returns></returns>
    public static RewriteOptions AddRedirectToWwwPermanent(this RewriteOptions options)
    {
        return AddRedirectToWww(options, statusCode: StatusCodes.Status308PermanentRedirect);
    }
