<?php

namespace GS\ApiBundle\Twig;

class GSExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('short_path', array($this, 'shortPathFilter')),
        );
    }

    public function shortPathFilter($route)
    {
        $i = strrpos($route, '/api');
        if (false !== $i) {
            $route = substr($route, $i+4);
        }
        return $route;
    }
}
