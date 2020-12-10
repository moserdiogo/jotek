<?php

class LoginAttempt extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDLA;

    /**
     *
     * @var string
     */
    public $dTLA;

    /**
     *
     * @var string
     */
    public $userName;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $browser;

    /**
     *
     * @var string
     */
    public $iP;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Login_Attempt");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Login_Attempt';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LoginAttempt[]|LoginAttempt|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LoginAttempt|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
