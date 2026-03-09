<?php

namespace Formulair\Model;


abstract class BaseModel extends \RedBean\SimpleModel
{
    public $slug;
    public $createdAt;
    public $updatedAt;
}
