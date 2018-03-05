<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class UsuarioXSeguidores extends Model
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }
}