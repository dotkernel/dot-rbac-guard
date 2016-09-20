<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-rbac-guard
 * @author: n3vrax
 * Date: 5/25/2016
 * Time: 7:04 PM
 */

namespace Dot\Rbac\Guard;

use Dot\Rbac\Guard\Exception\RuntimeException;
use Zend\Diactoros\Uri;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class RouteOptionParserTrait
 * @package Dot\Rbac\Guard
 */
trait RouteOptionParserTrait
{
    /**
     * @param $route
     * @param UrlHelper $urlHelper
     * @return Uri|static
     * @throws \Exception
     */
    public function getUri($route, UrlHelper $urlHelper)
    {
        $params = [];
        $queryParams = [];
        if (is_string($route)) {
            $routeName = $route;
        } elseif (is_array($route)) {
            $routeName = isset($route['name']) ? $route['name'] : null;
            $params = isset($route['params']) ? $route['params'] : [];
            $queryParams = isset($route['query_params']) ? $route['query_params'] : [];
        }

        if (empty($routeName) || !is_string($routeName)) {
            throw new RuntimeException('Invalid route option');
        }

        $uri = new Uri($urlHelper->generate($routeName, $params));
        if (!empty($queryParams)) {
            $query = http_build_query($queryParams);
            $uri = $uri->withQuery($query);
        }

        return $uri;
    }

    /**
     * @param $route
     * @return mixed|null
     */
    public function getRouteName($route)
    {
        if (is_string($route)) {
            return $route;
        } elseif (is_array($route)) {
            return isset($route['name']) ? $route['name'] : null;
        }

        return null;
    }
}