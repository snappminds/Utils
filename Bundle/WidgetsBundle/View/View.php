<?php

namespace Snappminds\Utils\Bundle\WidgetsBundle\View;

class View
{

    private $vars = array();

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return View La vista actual
     */
    public function set($name, $value)
    {
        $this->vars[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return Boolean
     */
    public function has($name)
    {
        return array_key_exists($name, $this->vars);
    }

    /**
     * @param $name
     * @param $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (false === $this->has($name)) {
            return $default;
        }

        return $this->vars[$name];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->vars;
    }

    /**
     * Alias of all so it is possible to do `form.vars.foo`
     *
     * @return array
     */
    public function getVars()
    {
        return $this->all();
    }

}
