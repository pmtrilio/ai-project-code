<?php

namespace Illuminate\Session;

use Illuminate\Support\Manager;

/**
 * @mixin \Illuminate\Session\Store
 */
class SessionManager extends Manager
{
    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @return \Illuminate\Session\Store
     */
    protected function callCustomCreator($driver)
    {
        return $this->buildSession(parent::callCustomCreator($driver));
    }

    /**
     * Create an instance of the "null" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createNullDriver()
    {
        return $this->buildSession(new NullSessionHandler);
    }

    /**
     * Create an instance of the "array" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createArrayDriver()
    {
        return $this->buildSession(new ArraySessionHandler(
            $this->config->get('session.lifetime')
        ));
    }

    /**
     * Create an instance of the "cookie" session driver.
     *
     * @return \Illuminate\Session\Store
     */
    protected function createCookieDriver()
    {
        return $this->buildSession(new CookieSessionHandler(
            $this->container->make('cookie'),
            $this->config->get('session.lifetime'),
            $this->config->get('session.expire_on_close')
        ));
    }

    /**
     * Create an instance of the file session driver.
     *