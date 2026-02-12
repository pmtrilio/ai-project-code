        }

        return false;
    }

    public function stream_stat(): array
    {
        try {
            $headers = $this->response->getHeaders(false);
        } catch (ExceptionInterface $e) {
            trigger_error($e->getMessage(), \E_USER_WARNING);
            $headers = [];
        }

        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => 33060,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => (int) ($headers['content-length'][0] ?? -1),
            'atime' => 0,
            'mtime' => strtotime($headers['last-modified'][0] ?? '') ?: 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
        ];
    }

    private function __construct()
    {
    }
}
