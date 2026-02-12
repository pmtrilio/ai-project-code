{
    /// <summary>
    /// Returns the matching <see cref="PatternSegment"/> for the given <paramref name="serverVariable"/>
    /// </summary>
    /// <param name="serverVariable">The server variable</param>
    /// <param name="context">The parser context which is utilized when an exception is thrown</param>
    /// <param name="uriMatchPart">Indicates whether the full URI or the path should be evaluated for URL segments</param>
    /// <param name="alwaysUseManagedServerVariables">Determines whether server variables are sourced from the managed server</param>
    /// <exception cref="FormatException">Thrown when the server variable is unknown</exception>
    /// <returns>The matching <see cref="PatternSegment"/></returns>
    public static PatternSegment FindServerVariable(string serverVariable, ParserContext context, UriMatchPart uriMatchPart, bool alwaysUseManagedServerVariables)
    {
        Func<PatternSegment>? managedVariableThunk = default;

        switch (serverVariable)
        {
            // TODO Add all server variables here.
            case "ALL_RAW":
                managedVariableThunk = () => throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
                break;
            case "APP_POOL_ID":
                managedVariableThunk = () => throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
                break;
            case "CONTENT_LENGTH":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.ContentLength);
                break;
            case "CONTENT_TYPE":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.ContentType);
                break;
            case "HTTP_ACCEPT":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.Accept);
                break;
            case "HTTP_COOKIE":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.Cookie);
                break;
            case "HTTP_HOST":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.Host);
                break;
            case "HTTP_REFERER":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.Referer);
                break;