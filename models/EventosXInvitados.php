<?php

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class EventosXInvitados extends Model
{
    public function initialize()
    {
       $this->useDynamicUpdate(true);
        $this->skipAttributesOnUpdate(array('id'));
    }
}