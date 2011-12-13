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

/**
 * @copyright Copyright (c) Justin Lloyd 2011
 * @property $class array Class property
 * @property $class_name string Class name property
 * @property $class_file string Class file property
 * @property $is_namespaced bool Is namespaced property
 * @property $app_namespace string Application namespace property
 * @property $app_path string Application namespace property
 * @property $file_suffix string File suffix property
 * @property $base_path string
 */
namespace Sparc\Autoloader;
use Sparc\Exception\AutoloadException as AutoloadException;
require "autoloader.class.php";
use Sparc;
class Loader extends Autoloader
{
    protected $class;
    protected $class_name;
    protected $class_file;
    protected $is_namespaced = false;
    protected $app_namespaces = null;
    protected $app_path;
    protected $file_suffix = '.class.php';
    protected $base_path;
    protected $custom_classes;

    public function __construct()
    {
        $this->getBasePath();
        $this->setLoader();
    }

    /**
     * Explodes the current directory to return the base framework path
     * @method getBasePath 
     * @return string $base_path
    */
    public function getBasePath()
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
        array_pop($path);
        return $this->base_path = implode(DIRECTORY_SEPARATOR, $path);
    }
    
    /**
     * Gets path to custom class (if it exists)
     * @method getCustomClass
     * @param string $class
     * @return mixed $path path to custom class or bool false if not matched
    */
    public function getCustomClass($class)
    {
        if (isset($this->custom_classes[$class])) {
            return $this->custom_classes[$class];
        } else {
            return false;
        }
    }
    
    /**
     * Gets path to custom class (if it exists)
     * @method setCustomClass
     * @param string $class Class name
     * @param string $path Class path
     * @return object $this
    */
    public function setCustomClass($class, $path)
    {
        if (is_readable($path)) {
            $this->custom_classes[$class] = $path;
        } else if (is_readable(realpath($path))) {
            $this->custom_classes[$class] = realpath($path);
        } else {
            throw new \Exception('File '.$path.' path does not exist or is not readable');
        }
        return $this;
    }    
    
    /**
     * Sets the internal autoloader path using spl_autoload_register function
     * @method setLoader 
     * @return object $this
    */
    protected function setLoader()
    {
        $loader = array($this, 'sparcLoader');
        spl_autoload_register($loader);
        if (!preg_match('/' . $this->base_path . '/', get_include_path())) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $this->base_path);
        }
        return $this;

    }

    /**
     * Sets the base path of your application
     * @method setAppPath 
     * @return object $this
    */
    public function setAppPath($path)
    {

        if (substr($path, -1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $this->app_path = $path;
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->app_path);
        return $this;
    }

    /**
     * Returns the base app path of your application
     * @method getAppPath 
     * @return object $this
    */
    public function getAppPath()
    {
        return $this->app_path;
    }

    /**
     * Change the default file suffix for the autoloader
     * @method setFileSuffix 
     * @return object $this
    */
    public function setFileSuffix($suffix)
    {
        $this->file_suffix = $suffix;
        return $this;
    }

    /**
     * Define the namespace (if used) of your application
     * @method setAppNamespace
     * @param string $namespace
     * @param strin $path
     * @return object $this
    */
    public function setAppNamespace($namespace, $path)
    {
        $this->app_namespaces[$namespace] = $path;
        return $this;
    }

    /**
     * Define an array of namespaces and their path
     * @method setAppNamespaces
     * @return object $this
    */
    public function setAppNamespace(array $namespaces)
    {
        foreach ($namespaces as $namespace => $path)
        $this->app_namespaces[$namespace] = $path;
        return $this;
    }

    /**
     * Checks if class uses a namespace
     * @method isNamespaced 
     * @return bool $is_namespaced (If a class uses a namespace)
    */
    protected function isNamespaced()
    {
        return $this->is_namespaced;
    }

    /**
     * Public wrapper around the sparcLoader method
     * @method preLoad 
    */
    public function preLoad($class)
    {
        $this->sparcLoader($class);
    }

    /**
     * Wrapper for non namespaced classes in current namespace
     * @param string $class Class name
     * @param bool $pre_load whether or not to preload the class
     * @return bool
     */
    public function classExists($class, $pre_load = true)
    {

        $this->class_name = $class;
        if (!$this->isNamespaced()) {
            return class_exists('\\' . $class, $pre_load);
        } else {
            return class_exists($class, $pre_load);
        }
    }

    /**
     * Autoloader wrapper - determins which loader to use based on namespace
     * @method sparcLoader
     */
    protected function sparcLoader($class)
    {
        $this->class = explode('\\', $class);
        $this->class_name = $class;
        if (isset($this->class[1])) {
            $this->is_namespaced = true;
            if ($this->class[0] == 'Sparc') {
                // Internal loader (Framework) classes
                $this->class_file = $this->_internalLoader();
            } else {
                $this->class_file = $this->autoloader();
            }
        } else {
            $this->is_namespaced = false;
            $this->class_file = $this->autoloader();
        }
        // Checks if class is namespaced
        if ($this->isNamespaced() == false) {
            // Prepends global namespace if no namespace is used
            echo $this->class_name = '\\' . $this->class_name;
        }
        // Checks if class exists
        if (!class_exists($this->class_name, false)) {
            // Checks if class file is readable
            if (is_readable($this->class_file)) {
                require $this->class_file;
            }
        }

    }

    /**
     * Registers additional autoloader functions
     * @method registerLoader
     * @return object $this
     */
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
        return $this;
    }

    /**
     * Internal autoloader method
     * @method _internalLoader private autoloader function
     */
    private function _internalLoader()
    {
        $class = preg_split('/(?=[A-Z])/', array_pop($this->class), -1, PREG_SPLIT_NO_EMPTY);
        $filename = strtolower(implode('_', $class)) . '.class.php';
        if (substr($this->base_path, strlen($this->class[0]) == strtolower($this->class[0]))) {
            array_shift($this->class);
        }

        return $this->base_path . DIRECTORY_SEPARATOR . strtolower((implode(DIRECTORY_SEPARATOR, $this->class))) . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Application autoloader function
     * @method autoloader
     */
    protected function autoloader()
    {

        if (!class_exists($this->class_name, false)) {
            $class = explode('_', $this->class_name);
            if (!$class[0]) {
                $class = explode('\\', $this->class_name);
            }

            $file = preg_split('/(?=[A-Z])/', array_pop($this->class), -1, PREG_SPLIT_NO_EMPTY);

            $file = strtolower(implode('_', $file));
            if (isset($this->app_namespaces[$this->class_name]) && is_readable(realpath($this->app_namespaces[$this->class_name]))) {
                return $this->app_namespaces[$this->class_name];
            } else if ($this->getCustomClass($this->class_name)) {
                return $this->getCustomClass($this->class_name);
            } else if (is_readable(realpath($this->app_path)  . $file . $this->file_suffix)) {
                return $file . $this->file_suffix;
            } else if (is_readable(realpath($this->app_path) . $file . '.php')) {
                return $file . '.php';
            } else if (is_readable(stream_resolve_include_path($file.$this->file_suffix))) {
                return stream_resolve_include_path($file.$this->file_suffix);
            } else {
                throw new AutoloadException;
            }

        }

    }

}
