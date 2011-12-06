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

namespace Sparc\Data;
class DataAdapter extends DataAbstraction
{
    public $driver_list = array(
        'mysql',
        'pgsql',
        'odbc',
    );
    
    protected $driver;
    protected $host;
    protected $db;
    protected $user;
    protected $pass;
    
	public function __construct($driver = null)
    {
        
    }
    
    public function setDriver($driver)
    {
        if (in_array($driver, $this->driver_list))
        {
            $this->driver = $driver;
            return $this;            
        } else {
            throw new \Exception('Invalid Driver: Please select a valid database driver or use the addDriver method to update the driver list');
        }

    }
    
    public function setDatabase($db)
    {
        $this->db = $db;
    }
    
    public function setHost($host) 
    {
        $this->host = $host;
        return $this;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function setPass($pass) 
    {
        $this->pass = $pass;
    }
    
    protected function getDSN()
    {
        return $this->driver.':dbname='.$this->db.';host='.$this->host;
    }
    
    protected function getUser()
    {
        return $this->user;
    }
    
    protected function getPass()
    {
        return $this->pass;
    }
    public function addDriver($driver)
    {
        $this->drivers[] = $driver;
    }
}
