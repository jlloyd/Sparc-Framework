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
class DataAbstraction extends Data
{
    protected $data_adaptor;
    protected $database;
    protected $connection;
    protected $connected = false;

    protected $debug;

    protected $action;
    protected $fields = array();
    protected $table;
    protected $table_alias;
    protected $where;
    protected $query;
    protected $result;
    protected $join;
    protected $table_as;
    
    
    protected $last_error;
    protected $last_query;
    protected $row_count;
    protected $use_transaction;
    protected $transaction_depth;
    protected $statement_object;

    public function __construct(DataAdaptor $dba)
    {
        $this->data_adaptor = $dba;
        $this->connect();
    }

    public function connect()
    {
        $this->database   = $this->data_adaptor->getDatabase();
        $this->connection = parent::__construct($this->data_adaptor->getDSN(), $this->data_adaptor->getUser(), $this->data_adaptor->getPass());
        $this->connected  = true;
    }

    public function switchConnection(DataAdaptor $dba)
    {
        $this->data_adaptor = $dba;
        $this->connect();
    }

    public function isConnected()
    {
        if (is_resource($this->connection)) {
            return true;
        } else {
            return false;
        }
    }

    public function disconnect()
    {
        $this->connection = null;
        $this->connected = false;
    }

    public function select($table, array $fields = null) 
    {
        if (is_array($fields)) {
            $this->fields = $fields;
        } else {
            $this->fields = '*';
        }
        $table_as = substr($table, 0, 3);
        $table_as = $this->setTableAlias($table);
        $this->table = $table;
        $this->table_alias = $table_as;
        $this->action = "SELECT";
        return $this;
    }

    public function update($table, array $fields)
    {
        $this->action = "UPDATE";
        $table_as = substr($table, 0, 3);
        $this->table_as[$table_as] = $table_as;
        $this->table  = $table;
        $this->table_alias = $table_as;
        $this->fields = implode(', ', $fields);
        return $this;
    }

    public function insert($table, array $fields)
    {
        $this->action = "INSERT";
        $table_as = substr($table, 0, 3);
        $this->table_as[$table_as] = $table_as;
        $this->table  = $table;
        $this->table_alias = $table_as;
        $this->fields = implode(', ', $fields);
        return $this;
    }
    
    public function delete($table)
    {
        $this->action = "DELETE";
        $this->table  = $table;
        return $this;
    }

    public function join($table, $field) 
    {
        if ($this->join == '') {
            $table_as = $this->setTableAlias($table);
            $this->join = "JOIN $table $table_as ON (".$this->table_alias.".$field = ".$table_as.".$field) ";
        } else {
            $this->join[] = " JOIN $table $table_as ON (".$this->table_alias.".$field = ".$table_as.".$field) ";
        }
        return $this;
    }

    public function where()
    {
        $this->where = $where;
    }

    public function limit($offset = 0, $limit = null)
    {
        if ($limit = null) {
            return $offset;
        } else if ($offset = null) {
            return '0,'.$limit;
        } else {
            return $limit.$offset;
        }
    }

    public function execute()
    {
        $this->query = $this->buildQuery();
        $this->statement_object = $this->database->prepare($this->query);
        $this->statement_object->execute($this->where);
    }

    protected function setTableAlias($table)
    {
        // Checks if alias already exists
        if (in_array($table, $this->table_as)) {
            return array_search($table, $this->table_as);
        }
        
        $table_as = substr($table, 0, 3);
        
        if (isset($this->table_as[$table_as])) {
            if (!isset($this->table_as[substr($table, 0, 4)])) {
               $table_as = substr($table, 0, 4);
               $this->table_as[$table_as] = $table_as;
               return $table_as;
            } else {
                $table_as = rand(1, 10000);
                $this->table_as[$table_as] = $table;
            }            
        } else {
            $this->table_as[$table_as] = $table;
            return $table_as;
        }

    }

    protected function changedDB($table)
    {
        $table = explode('.', $table);
        if (is_array($table)) {
            $database = array_pop($table);
            if ($database != $this->database) {
                $this->database = $database;
                return true;
            }
        }
        return false;

    }

    protected function getTableAlias($table)
    {
        if (in_array($table, $this->table_as)) {
            return array_search($table, $this->table_as);
        } else {
            return '';
        }
        
    }

    public function getRowCount() 
    {
        return $this->rowCount();
    }

    public function query($sql, $params)
    {
        
    }

    public function fetchArray() 
    {
        return $this->connection->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchFirst()
    {
        return $this->connection->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchObject($class)
    {
        $this->statement_object->fetchObject($class);
    }

    protected function buildQuery()
    {
        switch ($this->action) {
            case 'SELECT':
                return $this->prepareSelect();
                break;
            case 'INSERT':
                return '';
                break;
            case 'UPDATE':
                return '';
                break;
            case 'DELETE':
                return '';
                break;
        }
    }

    protected function prepareSelect()
    {
        
    }

    public function describe()
    {
        
    }

    public function columnType($table, $column)
    {
        
    }

    protected function bind(array $fields, array $bind) 
    {
        foreach ($fields as $field => $value) {
            
            if (isset($bind[$field])) {
                $this->statement_object->bindParam(':'.$field, $value);
            }
        }
    }

    public function preview()
    {
        return $this->query;
    }

    public function debug()
    {
        $this->debug = true;
    }

    protected function reset()
    {
        unset($this->action);
        unset($this->fields);
        unset($this->query);
        unset($this->table);
        unset($this->where);
    }
}
 