<?php

namespace Amranidev\ScaffoldInterface\Datasystem;

use Illuminate\Support\Facades\Schema;

/**
 * Class     DataSystem.
 *
 *
 * @author   Amrani Houssain <amranidev@gmail.com>
 */
class Datasystem
{
    /**
     * Main interface reqeust.
     *
     * @var
     */
    private $data;

    /**
     * on data specification.
     *
     * @var
     */
    private $onData;

    /**
     * ForrignKeys and relations.
     *
     * @var
     */
    private $foreignKeys;

    /**
     * Relational Columns.
     *
     * @var
     */
    private $relationAttributes;

    /**
     * Create DataSystem instance.
     *
     * @param array Data
     */
    public function __construct($data)
    {
        // unset TableName
        unset($data['TableName']);

        // unset template
        unset($data['template']);

        $this->data = $data;

        $this->relationData();

        $this->getAttr();
    }

    /**
     * deduce relational arttributes.
     * 
     * @return void
     */
    private function getAttr()
    {
        $array = collect($this->foreignKeys);
        $array = $array->each(function ($key, $value) {
            $Schema = collect(Schema::getColumnListing($key));
            $Schema = $Schema->reject(function ($value, $key) {
                return str_contains($value, 'id');
            });
           $this->relationAttributes[$value] = array_values($Schema->toArray());
        });
    }

    /**
     * deduce onData and ForeingKeys.
     *
     * @return void
     */
    private function relationData()
    {
        $onData = collect($this->data);

        $foreignKeys = collect($this->data);

        $onData = $onData->reject(function ($value, $key) {
            return !str_contains($key, 'on');
        });

        $foreignKeys = $foreignKeys->reject(function ($value, $key) {
            return !str_contains($key, 'tbl');
        });

        $this->onData = array_values($onData->toArray());

        $this->foreignKeys = array_values($foreignKeys->toArray());
    }

    /**
     * Data for migration and views.
     *
     * @param string specification
     *
     * @return array
     */
    public function dataScaffold($spec = null)
    {
        $array = collect($this->data);

        $array = $array->reject(function ($value, $key) use ($spec) {
            if ($spec == 'migration') {
                return !str_contains($key, 'opt');
            }

            return !str_contains($key, 'atr');
        });

        return array_values($array->toArray());
    }

    /**
     * Get foreignKeys.
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * Get relational attributes.
     */
    public function getRelationAttributes()
    {
        return $this->relationAttributes;
    }

    /**
     * Get onData.
     */
    public function getOnData()
    {
        return $this->onData;
    }

     /**
      * get request data.
      */
     public function getData()
     {
         return $this->data;
     }
}
