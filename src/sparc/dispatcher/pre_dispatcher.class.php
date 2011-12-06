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
namespace Sparc\Dispatcher;
class PreDispatcher extends DispatcherManager
{

    protected $event_response;

    public function __construct($run = false)
    {
        if ($run === true) {
            $this->callChain;
        }
    }

    public function addGlobalEvent($method, $params = null, $class = null)
    {
        $this->chain[] = array('method' => $method, 'class' => $class, 'params' => $params, );
    }

    public function callEvent()
    {
        if ($this->call['class'] !== null) {
            $call = new $this->call['class']();
            $this->event_response[] = $call->$method($this->call['params']);
        } else {
            $call = explode('::', $this->call['method']);

            if ($call[1]) {
                $this->event_response[] = $call[0]::$call[1]($this->call['params']);
            } else {
                $this->event_response[] = $call[0]($this->call['params']);
            }

        }

        return $this->chain();
    }

    protected function chain()
    {
        if (is_array($this->chain)) {

            foreach ($this->chain as $chain => $event) {
                $this->call = $event;
                $this->callEvent();
            }

            return $this->response;
        }
    }

}
 