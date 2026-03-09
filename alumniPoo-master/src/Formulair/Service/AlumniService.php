<?php

namespace Formulair\Service;

use Formulair\Model\Users;
use Formulair\Model\UserWorkExperience;
use Formulair\Model\UserContactInfo;
use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean;

class AlumniService
{
    public function getUserProfile(int $userId): ?OODBBean
    {
        return R::load('users', $userId);
    }

    public function updateUserProfile(int $userId, array $data): OODBBean
    {
        $fields = [];
        $values = [];

        if (isset($data['FirstName'])) {
            $fields[] = 'FirstName = ?';
            $values[] = $data['FirstName'];
        }
        if (isset($data['LastName'])) {
            $fields[] = 'LastName = ?';
            $values[] = $data['LastName'];
        }
        if (isset($data['iCity'])) {
            $fields[] = 'iCity = ?';
            $values[] = $data['iCity'] ?: null;
        }
        if (isset($data['YearWouldGraduateIn'])) {
            $fields[] = 'YearWouldGraduateIn = ?';
            $values[] = $data['YearWouldGraduateIn'] ?: null;
        }
        if (isset($data['iYearStart'])) {
            $fields[] = 'iYearStart = ?';
            $values[] = $data['iYearStart'] ?: null;
        }
        if (isset($data['iYearEnd'])) {
            $fields[] = 'iYearEnd = ?';
            $values[] = $data['iYearEnd'] ?: null;
        }

        if (!empty($fields)) {
            $values[] = $userId;
            R::exec(
                'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );
        }

        return R::load('users', $userId);
    }

    public function getUserWorkExperiences(int $userId): array
    {
        return R::getAll('SELECT * FROM user_work_experience WHERE iUser = ? ORDER BY dStart DESC', [$userId]);
    }

    public function addWorkExperience(int $userId, array $data): OODBBean
    {
        R::exec(
            'INSERT INTO user_work_experience (iUser, sCompany, iDivision, iCity, sCity, iCountry, dStart, iEnd, sDescription) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                $data['sCompany'] ?? '',
                $data['iDivision'] ?: null,
                $data['iCity'] ?: null,
                $data['sCity'] ?? '',
                $data['iCountry'] ?: null,
                $data['dStart'] ?: null,
                $data['iEnd'] ?: null,
                $data['sDescription'] ?? ''
            ]
        );

        return R::load('user_work_experience', R::getInsertID());
    }

    public function getUserContactInfo(int $userId): array
    {
        return R::getAll('SELECT * FROM user_contact_info WHERE iUser = ? ORDER BY dCreation DESC', [$userId]);
    }

    public function addContactInfo(int $userId, array $data): OODBBean
    {
        R::exec(
            'INSERT INTO user_contact_info (iUser, iType, sValue, dCreation) VALUES (?, ?, ?, ?)',
            [
                $userId,
                $data['iType'],
                $data['sValue'],
                date('Y-m-d H:i:s')
            ]
        );

        return R::load('user_contact_info', R::getInsertID());
    }

    public function getAllCities(): array
    {
        return R::findAll('cities', 'ORDER BY sLabel');
    }

    public function getAllJobDivisions(): array
    {
        return R::findAll('job_division', 'ORDER BY sLabel');
    }

    public function getAllContactTypes(): array
    {
        return R::findAll('contact_info_type', 'ORDER BY sLabel');
    }
}
