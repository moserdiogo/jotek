<?php

class MemberType extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDMBT;

    /**
     *
     * @var string
     */
    public $dTMBT;

    /**
     *
     * @var string
     */
    public $type;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Member_Type");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Member_Type';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberType[]|MemberType|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberType|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
