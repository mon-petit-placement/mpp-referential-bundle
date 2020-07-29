<?php

namespace Mpp\ReferentialBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ReferentialLoader extends Loader
{
    private $isLoaded = false;

    /**
     * @var array
     */
    private $referentials;

    public function __construct(array $referentials)
    {
        $this->referentials = $referentials;
    }

    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "mpp_referential" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->referentials as $referential => $values) {
            // prepare a new route
            $path = sprintf('/referential/%s.{_format}', $referential);
            $defaults = [
                '_controller' => 'Mpp\ReferentialBundle\Controller::getValues',
                'referential' => $referential
            ];
            $requirements = [
                'referential' => '\s+',
            ];
            $route = new Route($path, $defaults, $requirements, [], null, [], ['GET']);

            // add the new route to the route collection
            $routeName = sprintf('mpp_referential_%s', $referential);
            $routes->add($routeName, $route);
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'mpp_referential' === $type;
    }
}