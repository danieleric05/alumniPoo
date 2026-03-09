<?php

namespace Formulair\DataFixtures;

class UsersFixtures
{
  public function getType(){
    return 'users';
  }

  public function getFixtures(){
    return array(
      array(
        'sLogin'=> 'admin',
        'sPassword'=>'7878',
        'sLastName'=>'Daniel',
        'sFirstName'=>'Aboussou',
        'iCity'=> 'Abidjan',
        'iStatus'=> null,
        'YearwouldGraduateIn' =>'2001',
        'YearStart'=>'1993',
        'YearEnd'=> '2001',
        'dCreation'=>'2018'
      )
      );
  }
}
