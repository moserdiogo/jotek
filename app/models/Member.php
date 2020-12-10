<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;

class Member extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $iDMB;

    /**
     *
     * @var string
     */
    public $dTMB;

    /**
     *
     * @var integer
     */
    public $iDMBT;

    /**
     *
     * @var integer
     */
    public $iDMBA;

    /**
     *
     * @var integer
     */
    public $iDMBC;

    /**
     *
     * @var integer
     */
    public $iDMBS;

    /**
     *
     * @var integer
     */
    public $iDL;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $nameCompany;

    /**
     *
     * @var integer
     */
    public $gender;

    /**
     *
     * @var string
     */
    public $cPF;

    /**
     *
     * @var string
     */
    public $cNPJ;

    /**
     *
     * @var string
     */
    public $birthDate;

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
     * @var integer
     */
    public $online;

    /**
     *
     * @var string
     */
    public $updated;

    /**
     *
     * @var integer
     */
    public $entity;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("joteck");
        $this->setSource("Member");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'Member';
    }

    // Retirado por causa da chave unica, erro ao salvar outro com o valor NULL
    // // Validaçao antes de salvar
    // public function beforeSave() {
        
    //     $validator = new Validation();

    //     $validator->add(
    //         'CPF',
    //         new Uniqueness(
    //             [
    //                 'message' => 'Já existe um CPF cadastrado com esse número',
    //                 'code' => 3
    //             ]
    //         )
    //     );

    //     $validator->add(
    //         'CNPJ',
    //         new Uniqueness(
    //             [
    //                 'message' => 'Já existe um CNPJ cadastrado com esse número',
    //                 'code' => 3
    //             ]
    //         )
    //     );

    //     return $this->validate($validator);
    // }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Member[]|Member|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Member|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
