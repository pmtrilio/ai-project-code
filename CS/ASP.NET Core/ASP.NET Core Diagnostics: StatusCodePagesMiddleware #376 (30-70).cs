            throw new ArgumentException("Missing options.HandleAsync implementation.");
        }
    }

    /// <summary>
    /// Executes the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> for the current request.</param>
    /// <returns>A task that represents the execution of this middleware.</returns>
    public async Task Invoke(HttpContext context)
    {
        var statusCodeFeature = new StatusCodePagesFeature();
        context.Features.Set<IStatusCodePagesFeature>(statusCodeFeature);
        var endpoint = context.GetEndpoint();
        var shouldCheckEndpointAgain = endpoint is null;

        if (HasSkipStatusCodePagesMetadata(endpoint))
        {
            statusCodeFeature.Enabled = false;
        }

        // Attach pathFormat to HttpContext.Items early in the pipeline
        context.Items[nameof(StatusCodePagesOptions)] = _options.PathFormat;

        await _next(context);

        // Remove pathFormat from HttpContext.Items after handler execution
        context.Items.Remove(nameof(StatusCodePagesOptions));

        if (!statusCodeFeature.Enabled)
        {
            // Check if the feature is still available because other middleware (such as a web API written in MVC) could
            // have disabled the feature to prevent HTML status code responses from showing up to an API client.
            return;
        }

        if (shouldCheckEndpointAgain && HasSkipStatusCodePagesMetadata(context.GetEndpoint()))
        {
            // If the endpoint was null check the endpoint again since it could have been set by another middleware.
            return;
        }