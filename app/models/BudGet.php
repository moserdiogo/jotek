<?php

class BudGet extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDBG;

    /**
     *
     * @var string
     */
    public $dTBG;

    /**
     *
     * @var integer
     */
    public $iDPR;

    /**
     *
     * @var integer
     */
    public $iDS;

    /**
     *
     * @var integer
     */
    public $iDSP;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("BudGet");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'BudGet';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BudGet[]|BudGet|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BudGet|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
