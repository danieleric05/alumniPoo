<?php

namespace Formulair\Model;

class Users extends \RedBeanPHP\SimpleModel
{
    public $sLogin;
    public $sPassword;
    public $LastName;
    public $FirstName;
    public $iCity;
    public $iStatus;
    public $iRoleTemplate;
    public $YearWouldGraduateIn;
    public $iYearStart;
    public $iYearEnd;
    public $dCreation;
    public $dCotisationValidUntil;

}
