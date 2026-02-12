     */
    public function getPhpVersion(string $url): string
    {
        $defaultPhpVersion = $this->brew->linkedPhp();
        $phpVersion = PhpFpm::normalizePhpVersion($this->customPhpVersion($url));
        if (empty($phpVersion)) {
            $phpVersion = PhpFpm::normalizePhpVersion($defaultPhpVersion);
        }

        return $phpVersion;
    }

    /**
     * Resecure all currently secured sites with a fresh configuration.
     *
     * There are only two supported values: tld and loopback
     * And those must be submitted in pairs else unexpected results may occur.
     * eg: both $old and $new should contain the same indexes.
     */
    public function resecureForNewConfiguration(array $old, array $new): void
    {
        if (! $this->files->exists($this->certificatesPath())) {
            return;
        }

        $secured = $this->secured();

        $defaultTld = $this->config->read()['tld'];
        $oldTld = ! empty($old['tld']) ? $old['tld'] : $defaultTld;
        $tld = ! empty($new['tld']) ? $new['tld'] : $defaultTld;

        $defaultLoopback = $this->config->read()['loopback'];
        $oldLoopback = ! empty($old['loopback']) ? $old['loopback'] : $defaultLoopback;
        $loopback = ! empty($new['loopback']) ? $new['loopback'] : $defaultLoopback;

        foreach ($secured as $url) {
            $newUrl = str_replace('.'.$oldTld, '.'.$tld, $url);
            $siteConf = $this->getSiteConfigFileContents($url, '.'.$oldTld);

            if (! empty($siteConf) && strpos($siteConf, '# valet stub: secure.proxy.valet.conf') === 0) {
                // proxy config
                $this->unsecure($url);
                $this->secure(
                    $newUrl,
                    $this->replaceOldLoopbackWithNew(
                        $this->replaceOldDomainWithNew($siteConf, $url, $newUrl),
                        $oldLoopback,
                        $loopback
                    )
                );
            } else {
                // normal config
                $this->unsecure($url);
                $this->secure($newUrl);
            }
        }
    }

    /**
     * Parse Nginx site config file contents to swap old domain to new.
     */
    public function replaceOldDomainWithNew(string $siteConf, string $old, string $new): string
    {
        $lookups = [];
        $lookups[] = '~server_name .*;~';
        $lookups[] = '~error_log .*;~';
        $lookups[] = '~ssl_certificate_key .*;~';