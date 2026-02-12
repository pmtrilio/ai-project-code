        Match initMatchResults;
        if (path == PathString.Empty)
        {
            initMatchResults = InitialMatch.Match(path.ToString());
        }
        else
        {
            initMatchResults = InitialMatch.Match(path.ToString().Substring(1));
        }

        if (initMatchResults.Success)
        {
            var result = initMatchResults.Result(Replacement);
            var request = context.HttpContext.Request;

            if (StopProcessing)
            {
                context.Result = RuleResult.SkipRemainingRules;
            }

            if (string.IsNullOrEmpty(result))
            {
                result = "/";
            }

            if (result.Contains(Uri.SchemeDelimiter, StringComparison.Ordinal))
            {
                string scheme;
                HostString host;
                PathString pathString;
                QueryString query;
                UriHelper.FromAbsolute(result, out scheme, out host, out pathString, out query, out _);

                request.Scheme = scheme;
                request.Host = host;
                request.Path = pathString;
                request.QueryString = query.Add(request.QueryString);
            }
            else
            {
                var split = result.IndexOf('?');
                if (split >= 0)
                {
                    var newPath = result.Substring(0, split);
                    if (newPath[0] == '/')
                    {
                        request.Path = PathString.FromUriComponent(newPath);
                    }
                    else
                    {
                        request.Path = PathString.FromUriComponent('/' + newPath);
                    }
                    request.QueryString = request.QueryString.Add(
                        QueryString.FromUriComponent(
                            result.Substring(split)));
                }
                else
                {
                    if (result[0] == '/')
                    {
                        request.Path = PathString.FromUriComponent(result);