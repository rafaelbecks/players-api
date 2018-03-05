<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Usuarios extends Model
{
    public function initialize()
    {
        
        $this->useDynamicUpdate(true);
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete(
            array(
                'field' => 'estatus',
                'value' => 0
            )
        ));
    }

    public function validation() {
        $validation = new \Phalcon\Validation();
        $validation->add('email', new \Phalcon\Validation\Validator\Uniqueness([
           'message' => 'Duplicated email',
           'model' => $this
        ]));

        $validation->add('username', new \Phalcon\Validation\Validator\Uniqueness([
           'message' => 'Duplicated username',
           'model' => $this
        ]));
        return $this->validate($validation);
    }
}