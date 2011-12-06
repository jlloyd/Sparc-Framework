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
namespace Sparc\Http;
class Header extends Http
{
    protected $_headers = array();

    public function __construct()
    {

    }

    public function listHeaders()
    {
        return $this->headers;
    }

    public function addHeader($header, $value = '')
    {
        if (!headers_sent()) {
            if (!$value == '') {
                $this->_headers[] = $header;
            } else {
                $this->_headers[] = array('header' => $header, 'value' => $value);
            }

        } else {
            throw new Sparc_Header_Exception('Headers Already Sent', 1);
        }

    }

    public function deleteHeader($key)
    {
        unset($this->_headers[$key]);
    }

    public function deleteHeaders()
    {
        unset($this->_headers);
    }

    public function setHeaders()
    {
        if (!headers_sent()) {

            foreach ($this->_header as $key => $value) {
                if (!is_array($value)) {
                    header("$value");
                } else {
                    header("$value[header]: $value[value]");
                }

            }
        } else {
            throw new Sparc_Header_Exception('Headers Already Sent', 1);
        }

    }

    public function Redirect($url)
    {
        if (!headers_sent()) {
            header('location: ' . url);
            exit ;
        } else {
            // Add meta tag redirect method
        }
    }

    public function Reload()
    {
        if (!headers_sent()) {
            $link = $_SERVER['PHP_SELF'];
            header("Refresh: 0; url=$link");
            exit ;
        } else {
            // Add meta tag reload method
        }
    }

}
