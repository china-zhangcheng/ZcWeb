<?php

class Document extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $imgurl;

    /**
     *
     * @var string
     */
    public $url;

    /**
     *
     * @var integer
     */
    public $urltype;

    /**
     *
     * @var integer
     */
    public $doctype;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $time;

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var string
     */
    public $bpath;

    /**
     *
     * @var integer
     */
    public $visit;

    /**
     *
     * @var integer
     */
    public $year;

    /**
     *
     * @var integer
     */
    public $month;

    /**
     *
     * @var integer
     */
    public $ip;

    /**
     *
     * @var integer
     */
    public $isdel;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("vip_document");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'vip_document';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Document[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Document
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
