<?php

class MemberContact extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDMBC;

    /**
     *
     * @var string
     */
    public $dTMBC;

    /**
     *
     * @var integer
     */
    public $iDMB;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $secondPhone;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Member_Contact");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Member_Contact';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberContact[]|MemberContact|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MemberContact|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
