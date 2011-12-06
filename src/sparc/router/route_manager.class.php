<?php
/**
Copyright (c) 2011, Justin Lloyd
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE. 
**/

namespace Sparc\Router;
use Sparc\Exception\ControllerException as ControllerException;
use Sparc\Http\Request as Request;
use Sparc\Dispatcher\DispatcherManager as Dispatcher;
use Sparc\Autoloader\Loader as Loader;
class RouteManager extends Router
{

    protected $routes = null;

    public function __construct()
    {
        parent::__construct();
    }

    protected function parseRoute()
    {

        if (substr($this->route, 0, 1) == '/') {
            $this->route = substr($this -> route, 1);
        }
        $route = explode('/', $this->route);
        if (is_array($route)) {
            $this->controller = array_shift($route) . 'Controller';
            $this->method = array_shift($route) . 'Action';
            if (is_array($route)) {
                foreach ($route as $param) {
                    $this->params[] = $param;
                }
            }

        } else {
            $this->controller = $this -> route . 'Controller';
            $this->method = 'indexAction';
        }

    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    protected function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    protected function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    protected function setMethod($action)
    {
        $this->method = $action;
        return $this;
    }

    protected function setParams(array $params = null)
    {
        $this ->params = $params;
        return $this;
    }

    public function addRoute(Router $route)
    {
        $this->routes[] = $route;
    }

    private function _isCallable()
    {
        $loader = Loader::getInstance();
        if ($loader->classExists($this->controller)) {
            if (method_exists($this->controller, $this->method)) {
                return true;
            } else {
                return false;
            }

        } else {
            throw new ControllerException();
        }
    }

    public function dispatch()
    {
        if ($this->routes === null) {
            $this->parseRoute();
            if ($this->_isCallable()) {
                $this->callable = true;
            }
        } else {
            foreach ($this->routes as $route) {
                if (method_exists($route, 'match')) {
                    $match = $route->match($this);
                } else if (method_exists($route, 'test')) {
                    $match = $route->test($this);
                }

                if ($match === true) {
                    break;
                }
            }

            if ($this->_isCallable()) {
                $this->callable = true;
            }
        }
    }

}
