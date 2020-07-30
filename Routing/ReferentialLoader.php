<?php

namespace Mpp\ReferentialBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ReferentialLoader extends Loader
{
    /**
     * @var bool
     */
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

        foreach ($this->referentials as $item => $values) {
            $path = sprintf('/referential/%s.{_format}', $item);
            $defaults = [
                '_controller' => 'mpp_referential.action.entrypoint::getValues',
                '_referential_item' => $item,
                '_format' => 'json',
            ];
            $requirements = [
                'referential' => '\s+',
                '_format' => 'json|xml',
            ];
            $route = new Route($path, $defaults, $requirements, [], null, [], ['GET']);

            $routes->add(self::buildRouteName($item), $route);
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'mpp_referential' === $type;
    }

    public static function buildRouteName($item)
    {
        return sprintf('mpp_referential_%s', $item);
    }
}