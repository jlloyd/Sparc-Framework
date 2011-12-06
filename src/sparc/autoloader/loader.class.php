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
namespace Sparc\Autoloader;
use Sparc\Exception\AutoloadException as AutoloadException;
require "autoloader.class.php";
use Sparc;
class Loader extends Autoloader
{
    protected $class;
    protected $class_name;
    protected $class_file;
    protected $namespace = false;
    protected $app_namespace = null;
    protected $file_suffix = '.class.php';
    protected $base_path;

    public function __construct()
    {
        $this->getBasePath();
        $this->setLoader();
    }

    public function getBasePath()
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        array_pop($path);
        $this->base_path = implode(DIRECTORY_SEPARATOR, $path);
    }

    protected function setLoader()
    {
        $loader = array($this, 'sparcLoader');
        spl_autoload_register($loader);
        if (!preg_match('/' . $this->base_path . '/', get_include_path())) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->base_path);
        }

    }

    public function setAppPath($path)
    {

        if (substr($path, -1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $this->app_path = $path;
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->app_path);
        return $this;
    }

    public function getAppPath()
    {
        return $this->app_path;
    }

    public function setFileSuffix($suffix)
    {
        $this->file_suffix = $suffix;
        return $this;
    }

    public function setAppNamespace($namespace)
    {
        $this->app_namespace = $namespace;
        return $this;
    }

    protected function isNamespaced()
    {
        return $this->namespace;
    }

    public function preLoad($class)
    {
        $this->sparcLoader($class);
    }

    /*
     * Wrapper for non namespaced classes in current namespace
     * @return bool
     */

    public function classExists($class, $pre_load = true)
    {
        if ($pre_load === true) {
            $this->preLoad($class);
        }
        $this->class_name = $class;
        if ($this->isNamespaced()) {
            return class_exists('\\' . $class, false);
        } else {
            return class_exists($class, false);
        }
    }

    private function sparcLoader($class)
    {
        $this->class = explode('\\', $class);
        $this->class_name = $class;
        if (isset($this->class[1])) {
            $this->namespace = true;
            if ($this->class[0] == 'Sparc') {
                $this->class_file = $this->_internalLoader();
            } else {
                $this->class_file = $this->autoloader();
            }
        } else {
            $this->namespace = false;
            $this->class_file = $this->autoloader();
        }

        if ($this->isNamespaced() == false) {
            echo $this->class_name = '\\' . $this->class_name;
        }

        if (!class_exists($this->class_name, false)) {

            if (is_readable($this->class_file)) {
                require $this->class_file;
            }
        }

    }

    public function registerLoader($loader, $path)
    {

        if (is_array($loader)) {
            if (class_exists($loader[0], false)) {
                spl_autoload_register($loader);
            } else {
                require $path;
                spl_autoload_register($loader, true);
            }

        } else {
            spl_autoload_register($loader);
        }

    }

    private function _internalLoader()
    {
        $class = preg_split('/(?=[A-Z])/', array_pop($this->class), -1, PREG_SPLIT_NO_EMPTY);
        $filename = strtolower(implode('_', $class)) . '.class.php';
        if (substr($this->base_path, strlen($this->class[0]) == strtolower($this->class[0]))) {
            array_shift($this->class);
        }

        return $this->base_path . DIRECTORY_SEPARATOR . strtolower((implode(DIRECTORY_SEPARATOR, $this->class))) . DIRECTORY_SEPARATOR . $filename;
    }

    protected function autoloader()
    {

        if (!class_exists($this->class_name, false)) {
            $class = explode('_', $this->class_name);
            if (!$class[0]) {
                $class = explode('\\', $this->class_name);
            }

            $file = preg_split('/(?=[A-Z])/', array_pop($this->class), -1, PREG_SPLIT_NO_EMPTY);

            $file = $this->app_path . strtolower(implode('_', $file));
            if (is_readable($file . $this->file_suffix)) {
                return $file . $this->file_suffix;
            } else if (is_readable($file . '.php')) {
                return $file . '.php';
            } else {
                throw new AutoloadException;
            }

        }

    }

}
