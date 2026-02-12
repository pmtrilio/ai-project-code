            return \sprintf('a string ("%s%s")', mb_substr($var, 0, 255), mb_strlen($var) > 255 ? '...' : '');
        }

        if (is_numeric($var)) {
            return \sprintf('a number (%s)', (string) $var);
        }

        return (string) $var;
    }
}
