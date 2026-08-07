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
        $user = R::load('users', $userId);

        foreach (['FirstName', 'LastName', 'iCity', 'YearWouldGraduateIn', 'iYearStart', 'iYearEnd'] as $field) {
            if (isset($data[$field])) {
                $user->$field = $data[$field] !== '' ? $data[$field] : null;
            }
        }

        R::store($user);

        return $user;
    }

    public function updateMembershipExpiry(int $userId, ?string $date): ?OODBBean
    {
        $user = R::load('users', $userId);

        if (!$user->id) {
            return null;
        }

        $user->dCotisationValidUntil = $date;

        R::store($user);

        return $user;
    }

    public function getActiveMembers(): array
    {
        return R::find('users', 'd_cotisation_valid_until >= ? ORDER BY _last_name, _first_name', [date('Y-m-d')]);
    }

    public function getUserWorkExperiences(int $userId): array
    {
        $experiences = R::find('user_work_experience', 'i_user = ? ORDER BY d_start DESC', [$userId]);

        return array_map([$this, 'workExperienceToArray'], array_values($experiences));
    }

    public function addWorkExperience(int $userId, array $data): OODBBean
    {
        $exp = R::dispense('user_work_experience');
        $exp->iUser = $userId;
        $exp->sCompany = $data['sCompany'] ?? '';
        $exp->iDivision = $data['iDivision'] ?: null;
        $exp->iCity = $data['iCity'] ?: null;
        $exp->sCity = $data['sCity'] ?? '';
        $exp->iCountry = $data['iCountry'] ?: null;
        $exp->dStart = $data['dStart'] ?: null;
        $exp->iEnd = $data['iEnd'] ?: null;
        $exp->sDescription = $data['sDescription'] ?? '';

        R::store($exp);

        return $exp;
    }

    public function getWorkExperienceById(int $id, int $userId): ?OODBBean
    {
        $exp = R::findOne('user_work_experience', 'id = ? AND i_user = ?', [$id, $userId]);

        return $exp ?: null;
    }

    public function updateWorkExperience(int $id, int $userId, array $data): ?OODBBean
    {
        $exp = $this->getWorkExperienceById($id, $userId);

        if ($exp === null) {
            return null;
        }

        $exp->sCompany = $data['sCompany'] ?? '';
        $exp->iDivision = $data['iDivision'] ?: null;
        $exp->iCity = $data['iCity'] ?: null;
        $exp->sCity = $data['sCity'] ?? '';
        $exp->iCountry = $data['iCountry'] ?: null;
        $exp->dStart = $data['dStart'] ?: null;
        $exp->iEnd = $data['iEnd'] ?: null;
        $exp->sDescription = $data['sDescription'] ?? '';

        R::store($exp);

        return $exp;
    }

    public function deleteWorkExperience(int $id, int $userId): bool
    {
        $exp = $this->getWorkExperienceById($id, $userId);

        if ($exp === null) {
            return false;
        }

        R::trash($exp);

        return true;
    }

    public function getUserContactInfo(int $userId): array
    {
        $infos = R::find('user_contact_info', 'i_user = ? ORDER BY d_creation DESC', [$userId]);

        return array_map([$this, 'contactInfoToArray'], array_values($infos));
    }

    public function deleteContactInfo(int $id, int $userId): bool
    {
        $info = R::findOne('user_contact_info', 'id = ? AND i_user = ?', [$id, $userId]);

        if ($info === null) {
            return false;
        }

        R::trash($info);

        return true;
    }

    public function addContactInfo(int $userId, array $data): OODBBean
    {
        $info = R::dispense('user_contact_info');
        $info->iUser = $userId;
        $info->iType = $data['iType'];
        $info->sValue = $data['sValue'];
        $info->dCreation = date('Y-m-d H:i:s');

        R::store($info);

        return $info;
    }

    public function getAllCities(): array
    {
        return R::findAll('cities', 'ORDER BY s_label');
    }

    public function getAllJobDivisions(): array
    {
        return R::findAll('job_division', 'ORDER BY s_label');
    }

    public function getAllContactTypes(): array
    {
        return R::findAll('contact_info_type', 'ORDER BY s_label');
    }

    private function workExperienceToArray(OODBBean $exp): array
    {
        return [
            'id' => $exp->id,
            'sCompany' => $exp->sCompany,
            'iDivision' => $exp->iDivision,
            'iCity' => $exp->iCity,
            'sCity' => $exp->sCity,
            'iCountry' => $exp->iCountry,
            'dStart' => $exp->dStart,
            'iEnd' => $exp->iEnd,
            'sDescription' => $exp->sDescription,
        ];
    }

    private function contactInfoToArray(OODBBean $info): array
    {
        return [
            'id' => $info->id,
            'iType' => $info->iType,
            'sValue' => $info->sValue,
            'dCreation' => $info->dCreation,
        ];
    }
}
