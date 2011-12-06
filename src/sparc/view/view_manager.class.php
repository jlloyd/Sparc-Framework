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
namespace Sparc\View;
class ViewManager extends View
{

    public function render($view = null)
    {

        if ($view !== null) {
            $this->renderView();
        }

        if (isset($this->includes)) {
            $this->renderView();
        }

    }

    public function addHelper($method, $params, $class = null)
    {
        if ($class != null) {

        }
    }

    public function addHeader($header)
    {
        $this->header = $header;
    }

    public function addFooter($footer)
    {
        $this->footer = $footer;
    }

    public function prependView($view)
    {
        array_shift($this->includes, $view);
    }

    public function appendView($view)
    {
        array_push($this->includes, $view);
    }

    public function assign($key, $value)
    {
        $this->vars[$key] = $value;
    }

    public function assignByRef($key, &$value)
    {
        $this->vars[$key] = &$value;
    }

    protected function renderTemplate($template)
    {

    }

    protected function renderView($view = null)
    {
        ob_start();

        extract($this->vars);
        if (isset($this->header) && is_readable($this->header)) {
            include $this->header;
        }

        if ($view != null && file_exists($view)) {
            include $view;
        } else if (is_array($this->includes)) {
            foreach ($this->includes as $include) {
                include $include;
            }
            unset($this->includes);
        }

        if (isset($this->footer) && is_readable($this->footer)) {
            include $this->footer;
        }

        $this->view = ob_end_flush();
        return $this->view;
    }

}
