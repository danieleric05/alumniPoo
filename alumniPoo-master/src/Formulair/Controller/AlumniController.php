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

        if (!empty($validator->getErrors())) {
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

        if (!empty($validator->getErrors())) {
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

    public function editWorkExperience(string $id): string
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];
        $expId = (int) $id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEditWorkExperience($userId, $expId);
        }

        $experience = $this->alumniService->getWorkExperienceById($expId, $userId);

        if ($experience === null) {
            http_response_code(404);
            $this->redirect('/work-experience');
            return '';
        }

        $cities = $this->alumniService->getAllCities();
        $divisions = $this->alumniService->getAllJobDivisions();

        return $this->view('alumni/edit_work_experience', [
            'experience' => $experience,
            'cities' => $cities,
            'divisions' => $divisions,
        ]);
    }

    private function handleEditWorkExperience(int $userId, int $expId): string
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

        if (!empty($validator->getErrors())) {
            $cities = $this->alumniService->getAllCities();
            $divisions = $this->alumniService->getAllJobDivisions();

            return $this->view('alumni/edit_work_experience', [
                'experience' => (object) array_merge(['id' => $expId], $data),
                'cities' => $cities,
                'divisions' => $divisions,
                'errors' => $validator->getErrors(),
            ]);
        }

        $experience = $this->alumniService->updateWorkExperience($expId, $userId, $data);

        if ($experience === null) {
            http_response_code(404);
            $this->redirect('/work-experience');
            return '';
        }

        $this->logger->info("Work experience $expId updated for user: $userId");

        $this->redirect('/work-experience');
        return '';
    }

    public function deleteWorkExperience(string $id): void
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->alumniService->deleteWorkExperience((int) $id, $userId);
            $this->logger->info("Work experience $id deleted for user: $userId");
        }

        $this->redirect('/work-experience');
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

        if (!empty($validator->getErrors())) {
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

    public function deleteContactInfo(string $id): void
    {
        AuthMiddleware::requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->alumniService->deleteContactInfo((int) $id, $userId);
            $this->logger->info("Contact info $id deleted for user: $userId");
        }

        $this->redirect('/contact-info');
    }
}
