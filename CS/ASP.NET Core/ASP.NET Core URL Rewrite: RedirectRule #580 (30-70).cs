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

        if (initMatchResults.Success)
        {
            var newPath = initMatchResults.Result(Replacement);
            var response = context.HttpContext.Response;

            response.StatusCode = StatusCode;
            context.Result = RuleResult.EndResponse;

            string encodedPath;

            if (string.IsNullOrEmpty(newPath))
            {
                encodedPath = pathBase.HasValue ? pathBase.Value : "/";
            }
            else
            {
                var host = default(HostString);
                var schemeSplit = newPath.IndexOf(Uri.SchemeDelimiter, StringComparison.Ordinal);
                string scheme = request.Scheme;
                if (schemeSplit >= 0)
                {
                    scheme = newPath.Substring(0, schemeSplit);
                    schemeSplit += Uri.SchemeDelimiter.Length;
                    var pathSplit = newPath.IndexOf('/', schemeSplit);

                    if (pathSplit == -1)
                    {
                        host = new HostString(newPath.Substring(schemeSplit));