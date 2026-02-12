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
            case "HTTP_USER_AGENT":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.UserAgent);
                break;
            case "HTTP_CONNECTION":
                managedVariableThunk = () => new HeaderSegment(HeaderNames.Connection);
                break;
            case "HTTP_URL":
                managedVariableThunk = () => new UrlSegment(uriMatchPart);
                break;
            case "HTTPS":
                managedVariableThunk = () => new IsHttpsUrlSegment();
                break;
            case "LOCAL_ADDR":
                managedVariableThunk = () => new LocalAddressSegment();
                break;
            case "HTTP_PROXY_CONNECTION":
                managedVariableThunk = () => throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
                break;
            case "QUERY_STRING":
                managedVariableThunk = () => new QueryStringSegment();
                break;
            case "REMOTE_ADDR":
                managedVariableThunk = () => new RemoteAddressSegment();
                break;
            case "REMOTE_HOST":
                managedVariableThunk = () => throw new NotSupportedException(Resources.FormatError_UnsupportedServerVariable(serverVariable));
                break;
            case "REMOTE_PORT":
                managedVariableThunk = () => new RemotePortSegment();
                break;