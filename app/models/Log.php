<?php

class Log extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDL;

    /**
     *
     * @var string
     */
    public $dTL;

    /**
     *
     * @var integer
     */
    public $iDMB;

    /**
     *
     * @var string
     */
    public $iP;

    /**
     *
     * @var string
     */
    public $browser;

    /**
     *
     * @var string
     */
    public $lastActive;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Log");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Log[]|Log|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Log|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
