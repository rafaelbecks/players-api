<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Estados extends Model
{
    public function initialize()
    {
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete(
            array(
                'field' => 'estatus',
                'value' => 0
            )
        ));
    }
}