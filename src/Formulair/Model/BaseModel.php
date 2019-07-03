<?php

namespace  Fomulair\Model;


abstract class BaseModel extends \RedBean_SimpleModel
{
    public $slug;
    public $createdAt;
    public $updatedAt;
}