        foreach ($values as $key => $value) {
            $values[$key] = $this->formatValue($value, $format);
        }

        return implode(', ', $values);
    }
}
