<?php

class GeoState extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDGS;

    /**
     *
     * @var string
     */
    public $state;

    /**
     *
     * @var string
     */
    public $stateUF;

    /**
     *
     * @var integer
     */
    public $iBGE;

    /**
     *
     * @var integer
     */
    public $country;

    /**
     *
     * @var string
     */
    public $dDD;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Geo_State");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Geo_State';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return GeoState[]|GeoState|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return GeoState|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
