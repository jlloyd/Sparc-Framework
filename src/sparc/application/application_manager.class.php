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
namespace Sparc\Application;
use Sparc\Bootstrap;
use Sparc\Controller;
use Sparc\Router;
class ApplicationManager extends Application
{
    protected $bootstrap;
    protected $request;
    protected $response;
    protected $front_controller = 'Sparc\Controller\ControllerManager';
    protected $route_manager    = 'Sparc\Router\RouteManager';
    public function __construct($bootstrap = null)
    {
        if ($bootstrap != null) {
            $bootstrap = $this->bootstrap;
        }

    }

    public function setBootstrap($bootstrap)
    {
        if (is_object($bootstrap)) {
            $this->bootstrap = $bootstrap;
        } else {
            throw new \Exception('Bootstrap not initialized');
        }
    }

    public function setFrontController(Controller $controller)
    {
        $this->front_controller = $controller;
        return $this;
    }

    public function setRouteManager(Router $router)
    {
        $this->route_manager = $router;
        return $this;
    }

    public function route()
    {
        if (is_object($this->route_manager)) {
            return $this->route_mananger;
        } else {
            return new $this->route_manager($this);
        }

    }

    public function display()
    {
        echo $this->response;
    }

}
 