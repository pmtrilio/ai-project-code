
        var request = httpContext.Request;
        if (!request.QueryString.HasValue)
        {
            return NullProviderCultureResult;
        }

        string? queryCulture = null;
        string? queryUICulture = null;

        if (!string.IsNullOrWhiteSpace(QueryStringKey))
        {
            queryCulture = request.Query[QueryStringKey];
        }

        if (!string.IsNullOrWhiteSpace(UIQueryStringKey))
        {
            queryUICulture = request.Query[UIQueryStringKey];
        }

        if (queryCulture == null && queryUICulture == null)
        {
            // No values specified for either so no match
            return NullProviderCultureResult;
        }

        if (queryCulture != null && queryUICulture == null)
        {
            // Value for culture but not for UI culture so default to culture value for both
            queryUICulture = queryCulture;
        }
        else if (queryCulture == null && queryUICulture != null)
        {
            // Value for UI culture but not for culture so default to UI culture value for both
            queryCulture = queryUICulture;
        }

        var providerResultCulture = new ProviderCultureResult(queryCulture, queryUICulture);

        return Task.FromResult<ProviderCultureResult?>(providerResultCulture);
    }
}
