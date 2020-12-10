<?php

class ErrorLog extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDE;

    /**
     *
     * @var string
     */
    public $dTE;

    /**
     *
     * @var string
     */
    public $controller;

    /**
     *
     * @var string
     */
    public $action;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $optionalMessage;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Error_Log");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Error_Log';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ErrorLog[]|ErrorLog|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ErrorLog|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
