        {
            return NullProviderCultureResult;
        }

        var languages = acceptLanguageHeader.AsEnumerable();

        if (MaximumAcceptLanguageHeaderValuesToTry > 0)
        {
            // We take only the first configured number of languages from the header and then order those that we
            // attempt to parse as a CultureInfo to mitigate potentially spinning CPU on lots of parse attempts.
            languages = languages.Take(MaximumAcceptLanguageHeaderValuesToTry);
        }

        var orderedLanguages = languages.OrderByDescending(h => h, StringWithQualityHeaderValueComparer.QualityComparer)
            .Select(x => x.Value).ToList();

        if (orderedLanguages.Count > 0)
        {
            return Task.FromResult<ProviderCultureResult?>(new ProviderCultureResult(orderedLanguages));
        }

        return NullProviderCultureResult;
    }
}
