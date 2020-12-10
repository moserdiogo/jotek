<?php

class GeoCity extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDGC;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var integer
     */
    public $cityUF;

    /**
     *
     * @var integer
     */
    public $iBGE;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Geo_City");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Geo_City';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GeoCity[]|GeoCity|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GeoCity|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
