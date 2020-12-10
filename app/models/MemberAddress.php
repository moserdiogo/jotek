<?php

class MemberAddress extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDMBA;

    /**
     *
     * @var string
     */
    public $dTMBA;

    /**
     *
     * @var integer
     */
    public $iDMB;

    /**
     *
     * @var integer
     */
    public $iDGC;

    /**
     *
     * @var integer
     */
    public $iDGS;

    /**
     *
     * @var string
     */
    public $district;

    /**
     *
     * @var string
     */
    public $street;

    /**
     *
     * @var string
     */
    public $number;

    /**
     *
     * @var string
     */
    public $complement;

    /**
     *
     * @var string
     */
    public $zipCode;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Member_Address");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Member_Address';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberAddress[]|MemberAddress|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberAddress|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
