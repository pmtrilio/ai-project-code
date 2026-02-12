            $prefixLen = \strlen($prefix);
            $pattern = $prefix.$namespace.'*';
            foreach ($this->redis->_masters() as $ipAndPort) {
                $address = implode(':', $ipAndPort);
                $cursor = null;
                do {
                    $keys = $this->redis->scan($cursor, $address, $pattern, 1000);
                    if (isset($keys[1]) && \is_array($keys[1])) {
                        $cursor = $keys[0];
                        $keys = $keys[1];
                    }

                    if ($keys) {
                        if ($prefixLen) {
                            foreach ($keys as $i => $key) {
                                $keys[$i] = substr($key, $prefixLen);
                            }
                        }
                        $this->doDelete($keys);
                    }
                } while ($cursor);
            }

            return $cleared;
        }

        $hosts = $this->getHosts();
        $host = reset($hosts);
        if ($host instanceof \Predis\Client) {
            $connection = $host->getConnection();

            if ($connection instanceof ReplicationInterface) {
                $hosts = [$host->getClientFor('master')];
            } elseif ($connection instanceof Predis2ReplicationInterface) {
                $connection->switchToMaster();

                $hosts = [$host];
            }
        }

        foreach ($hosts as $host) {
            if (!isset($namespace[0])) {
                $cleared = $host->flushDb() && $cleared;
                continue;
            }

            $info = $host->info('Server');
            $info = !$info instanceof ErrorInterface ? $info['Server'] ?? $info : ['redis_version' => '2.0'];

            if ($host instanceof Relay) {
                $prefix = Relay::SCAN_PREFIX & $host->getOption(Relay::OPT_SCAN) ? '' : $host->getOption(Relay::OPT_PREFIX);
                $prefixLen = \strlen($host->getOption(Relay::OPT_PREFIX) ?? '');
            } elseif (!$host instanceof \Predis\ClientInterface) {
                $prefix = \defined('Redis::SCAN_PREFIX') && (\Redis::SCAN_PREFIX & $host->getOption(\Redis::OPT_SCAN)) ? '' : $host->getOption(\Redis::OPT_PREFIX);
                $prefixLen = \strlen($host->getOption(\Redis::OPT_PREFIX) ?? '');
            }
            $pattern = $prefix.$namespace.'*';

            if (!version_compare($info['redis_version'], '2.8', '>=')) {
                // As documented in Redis documentation (http://redis.io/commands/keys) using KEYS
                // can hang your server when it is executed against large databases (millions of items).
                // Whenever you hit this scale, you should really consider upgrading to Redis 2.8 or above.
                $unlink = version_compare($info['redis_version'], '4.0', '>=') ? 'UNLINK' : 'DEL';
                $args = $this->redis instanceof \Predis\ClientInterface ? [0, $pattern] : [[$pattern], 0];
                $cleared = $host->eval("local keys=redis.call('KEYS',ARGV[1]) for i=1,#keys,5000 do redis.call('$unlink',unpack(keys,i,math.min(i+4999,#keys))) end return 1", $args[0], $args[1]) && $cleared;
                continue;
            }
