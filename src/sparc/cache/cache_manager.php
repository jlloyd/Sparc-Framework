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
namespace Sparc\Cache;
class CacheManager extends Cache
{

    public function add($key, $value, $ttl = 60)
    {
        if ($this->cache_loaded) {
            
            if (apc_add($key, $value)) {
                return true;
            } else {
                throw new \Exception("Unable to store $key in cache");
            }
        }

    }

    public function delete($key)
    {
        if ($this->cache_loaded) {
            if ($this->exists($key)) {
                apc_delete($key);
            }

        }
    }

    public function addArray(array $array, $ttl = 60)
    {

        foreach ($array as $key => $value) {
            if (!$this->exists($key)) {
                apc_add($key, $value, $ttl);
            }

        }

    }

    public function exists($key)
    {
        return apc_exists($key);
    }

    public function flushCache($cache_type = 'system')
    {
        apc_clear_cache($cache_type);
    }

    public function cacheSession($session, $name)
    {
        $this->add($name, $session);
    }

}