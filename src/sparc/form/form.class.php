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
namespace Sparc\Form;
abstract class Form
{
    protected $get;
    protected $post;
    protected $request;
    protected $is_escaped = false;
    protected static $instance;
    public function __construct()
    {
        if (isset($_POST)) {
            $this->get = $_GET;
        }
        if (isset($_POST)) {
            $this->post = $_POST;
        }
        if (isset($_REQUESTR)) {
            $this->request = $_REQUEST;
        }

        $this->escapeInput();
    }

    private function escapeInput()
    {
        $func = array($this, 'escapeSpecialCharacters');

        $this->is_escaped = true;
    }

    public function escapeSpecialCharacters($key, &$value)
    {
        
        if ($this->filter_input == true) {
            filter_var_array($this->get, FILTER_SANITIZE_ENCODED);
            unset($_GET);
            filter_var_array($this->post, FILTER_SANITIZE_ENCODED);
            unset($_POST);
        }
        
        if ($this->strip_tags == true) {
            strip_tags($value);
        }

    }

    public function get($key, $type)
    {
        if (!is_escaped) {
            return false;
        }

        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
    }

    public function __call($func, $args)
    {
        $func = strtolower($func);
        if (substr($func, 0, 3) == 'get') {
            $this->get($args[0], $type);
        }
    }
    
    protected function getType($str)
    {
        
    }
}
