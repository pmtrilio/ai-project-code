        $pattern = $this->createPlainMatcher('endstory');

        return preg_replace($pattern, '$1<?php $__container->endMacro(); ?>$2', $value);
    }

    /**
     * Compile Envoy task start statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileTaskStart($value)
    {
        $pattern = $this->createMatcher('task');

        return preg_replace($pattern, '$1<?php $__container->startTask$2; ?>', $value);
    }

    /**
     * Compile Envoy task stop statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileTaskStop($value)
    {
        $pattern = $this->createPlainMatcher('endtask');

        return preg_replace($pattern, '$1<?php $__container->endTask(); ?>$2', $value);
    }

    /**
     * Compile Envoy before statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileBefore($value)
    {
        $pattern = $this->createPlainMatcher('before');

        return preg_replace($pattern, '$1<?php $_vars = get_defined_vars(); $__container->before(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; $2', $value);
    }

    /**
     * Compile Envoy before stop statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileBeforeStop($value)
    {
        return preg_replace($this->createPlainMatcher('endbefore'), '$1}); ?>$2', $value);
    }

    /**
     * Compile Envoy after statements into valid PHP.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileAfter($value)
    {
        $pattern = $this->createPlainMatcher('after');

        return preg_replace($pattern, '$1<?php $_vars = get_defined_vars(); $__container->after(function($task) use ($_vars) { extract($_vars, EXTR_SKIP)  ; $2', $value);
    }