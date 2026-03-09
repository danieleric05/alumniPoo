<?php

namespace Formulair\Controller;

use Formulair\Service\AlumniService;
use Formulair\Middleware\AuthMiddleware;

class AlumniController extends BaseController
{
    private AlumniService $alumniService;

    public function __construct(AlumniService $alumniService = null)
    {
        parent::__construct();
        $this->alumniService = $alumniService ?? new AlumniService();
    }

    public function dashboard(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->alumniService->getUserProfile($userId);

        return $this->view('alumni/dashboard', [
            'user' => $user,
        ]);
    }

    public function editProfile(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleUpdateProfile($userId);
        }

        $user = $this->alumniService->getUserProfile($userId);

        return $this->view('alumni/edit_profile', [
            'user' => $user,
        ]);
    }

    private function handleUpdateProfile(int $userId): string
    {
        $data = $this->getInput();

        $validator = $this->validate($data, [
            'FirstName' => 'required|alpha|max:100',
            'LastName' => 'required|alpha|max:100',
            'iCity' => 'numeric',
            'YearWouldGraduateIn' => 'numeric|min:1900|max:2100',
            'iYearStart' => 'numeric|min:1900|max:2100',
            'iYearEnd' => 'numeric|min:1900|max:2100',
        ]);

        if (!$validator->validate([])) {
            $user = $this->alumniService->getUserProfile($userId);
            return $this->view('alumni/edit_profile', [
                'user' => $user,
                'errors' => $validator->getErrors(),
            ]);
        }

        $user = $this->alumniService->updateUserProfile($userId, $data);
        $this->logger->info("User profile updated: {$user->sLogin}");

        $this->redirect('/profile');
        return '';
    }

    public function workExperience(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        $workExperiences = $this->alumniService->getUserWorkExperiences($userId);
        $cities = $this->alumniService->getAllCities();
        $divisions = $this->alumniService->getAllJobDivisions();

        return $this->view('alumni/work_experience', [
            'workExperiences' => $workExperiences,
            'cities' => $cities,
            'divisions' => $divisions,
        ]);
    }

    public function addWorkExperience(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleAddWorkExperience($userId);
        }

        $cities = $this->alumniService->getAllCities();
        $divisions = $this->alumniService->getAllJobDivisions();

        return $this->view('alumni/add_work_experience', [
            'cities' => $cities,
            'divisions' => $divisions,
        ]);
    }

    private function handleAddWorkExperience(int $userId): string
    {
        $data = $this->getInput();

        $validator = $this->validate($data, [
            'sCompany' => 'required|max:255',
            'iDivision' => 'required|numeric',
            'iCity' => 'numeric',
            'sCity' => 'max:255',
            'iCountry' => 'numeric',
            'dStart' => 'date',
            'iEnd' => 'numeric|min:1900|max:2100',
            'sDescription' => 'max:1000',
        ]);

        if (!$validator->validate([])) {
            $cities = $this->alumniService->getAllCities();
            $divisions = $this->alumniService->getAllJobDivisions();

            return $this->view('alumni/add_work_experience', [
                'cities' => $cities,
                'divisions' => $divisions,
                'errors' => $validator->getErrors(),
                'data' => $data,
            ]);
        }

        $workExp = $this->alumniService->addWorkExperience($userId, $data);
        $this->logger->info("Work experience added for user: $userId");

        $this->redirect('/work-experience');
        return '';
    }

    public function contactInfo(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        $contactInfos = $this->alumniService->getUserContactInfo($userId);
        $types = $this->alumniService->getAllContactTypes();

        return $this->view('alumni/contact_info', [
            'contactInfos' => $contactInfos,
            'types' => $types,
        ]);
    }

    public function addContactInfo(): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleAddContactInfo($userId);
        }

        $types = $this->alumniService->getAllContactTypes();

        return $this->view('alumni/add_contact_info', [
            'types' => $types,
        ]);
    }

    private function handleAddContactInfo(int $userId): string
    {
        $data = $this->getInput();

        $validator = $this->validate($data, [
            'iType' => 'required|numeric',
            'sValue' => 'required|max:255',
        ]);

        if (!$validator->validate([])) {
            $types = $this->alumniService->getAllContactTypes();

            return $this->view('alumni/add_contact_info', [
                'types' => $types,
                'errors' => $validator->getErrors(),
                'data' => $data,
            ]);
        }

        $contactInfo = $this->alumniService->addContactInfo($userId, $data);
        $this->logger->info("Contact info added for user: $userId");

        $this->redirect('/contact-info');
        return '';
    }
}
