<?php

namespace Dukt\Apis;

abstract class Api
{
    /**
     * Get Handle
     */
    public function getHandle()
    {
        $class = $this->getClass();

        $handle = strtolower($class);

        return $handle;
    }

    /**
     * Get provider class
     *
     * from : Dukt\Apis\Dribbble
     * to : Dribbble
     */
    public function getClass()
    {
        $nsClass = get_class($this);

        $class = substr($nsClass, strrpos($nsClass, "\\") + 1);

        return $class;
    }
}