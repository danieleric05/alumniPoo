<?php

namespace Formulair\DataFixtures;

require_once __DIR__ . '/../../../core/bootstrap.php';

use RedBeanPHP\Facade as R;
use Formulair\Core\Logger;

class LoadFixtures
{
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function loadAll(): void
    {
        $this->logger->info("Loading all fixtures...");

        R::begin();
        try {
            $this->loadContactTypes();
            $this->loadCountries();
            $this->loadCities();
            $this->loadJobDivisions();
            $this->loadEvents();
            $this->loadUsers();

            R::commit();
            $this->logger->info("All fixtures loaded successfully");
        } catch (\Exception $e) {
            R::rollback();
            $this->logger->error("Error loading fixtures: " . $e->getMessage());
            throw $e;
        }
    }

    private function loadContactTypes(): void
    {
        $types = [
            ['sLabel' => 'Email'],
            ['sLabel' => 'Téléphone'],
            ['sLabel' => 'LinkedIn'],
            ['sLabel' => 'Twitter'],
            ['sLabel' => 'Site web'],
        ];

        foreach ($types as $type) {
            $contactType = R::dispense('contact_info_type');
            $contactType->sLabel = $type['sLabel'];
            R::store($contactType);
        }

        $this->logger->info("Contact types loaded");
    }

    private function loadCountries(): void
    {
        $countries = [
            ['iNoCountry' => 33, 'sLabel' => 'France'],
            ['iNoCountry' => 1, 'sLabel' => 'États-Unis'],
            ['iNoCountry' => 44, 'sLabel' => 'Royaume-Uni'],
            ['iNoCountry' => 49, 'sLabel' => 'Allemagne'],
            ['iNoCountry' => 39, 'sLabel' => 'Italie'],
            ['iNoCountry' => 34, 'sLabel' => 'Espagne'],
        ];

        foreach ($countries as $country) {
            $c = R::dispense('country');
            $c->iNoCountry = $country['iNoCountry'];
            $c->sLabel = $country['sLabel'];
            R::store($c);
        }

        $this->logger->info("Countries loaded");
    }

    private function loadCities(): void
    {
        $cities = [
            ['sLabel' => 'Paris', 'iCountry' => 1],
            ['sLabel' => 'Lyon', 'iCountry' => 1],
            ['sLabel' => 'Marseille', 'iCountry' => 1],
            ['sLabel' => 'Toulouse', 'iCountry' => 1],
            ['sLabel' => 'New York', 'iCountry' => 2],
            ['sLabel' => 'Los Angeles', 'iCountry' => 2],
            ['sLabel' => 'Londres', 'iCountry' => 3],
            ['sLabel' => 'Berlin', 'iCountry' => 4],
        ];

        foreach ($cities as $city) {
            $c = R::dispense('cities');
            $c->sLabel = $city['sLabel'];
            $c->iCountry = $city['iCountry'];
            R::store($c);
        }

        $this->logger->info("Cities loaded");
    }

    private function loadJobDivisions(): void
    {
        $divisions = [
            ['sLabel' => 'Informatique', 'iParent' => null],
            ['sLabel' => 'Finance', 'iParent' => null],
            ['sLabel' => 'Marketing', 'iParent' => null],
            ['sLabel' => 'Ressources Humaines', 'iParent' => null],
            ['sLabel' => 'Développement web', 'iParent' => 1],
            ['sLabel' => 'Data Science', 'iParent' => 1],
        ];

        foreach ($divisions as $division) {
            $d = R::dispense('job_division');
            $d->sLabel = $division['sLabel'];
            $d->iParent = $division['iParent'];
            R::store($d);
        }

        $this->logger->info("Job divisions loaded");
    }

    private function loadEvents(): void
    {
        $events = [
            [
                'sLabel' => 'Réunion des alumni 2025',
                'txtDescription' => 'Grande réunion annuelle de tous les alumni',
                'dStart' => '2025-06-15 14:00:00',
                'dEnd' => '2025-06-15 18:00:00',
            ],
            [
                'sLabel' => 'Conférence professionnelle',
                'txtDescription' => 'Conférence sur les tendances du secteur',
                'dStart' => '2025-07-10 09:00:00',
                'dEnd' => '2025-07-10 17:00:00',
            ],
        ];

        foreach ($events as $event) {
            $e = R::dispense('tbl_events');
            $e->sLabel = $event['sLabel'];
            $e->txtDescription = $event['txtDescription'];
            $e->dStart = $event['dStart'];
            $e->dEnd = $event['dEnd'];
            $e->dCreation = date('Y-m-d H:i:s');
            R::store($e);
        }

        $this->logger->info("Events loaded");
    }

    private function loadUsers(): void
    {
        $users = [
            [
                'sLogin' => 'admin',
                'sPassword' => password_hash('admin123', PASSWORD_BCRYPT),
                'FirstName' => 'Admin',
                'LastName' => 'Système',
                'iStatus' => 1,
                'iRole' => 1,
                'YearWouldGraduateIn' => 2020,
                'iYearStart' => 2017,
                'iYearEnd' => 2020,
                'dCotisationValidUntil' => date('Y-m-d', strtotime('+1 year')),
            ],
            [
                'sLogin' => 'john_doe',
                'sPassword' => password_hash('password123', PASSWORD_BCRYPT),
                'FirstName' => 'John',
                'LastName' => 'Doe',
                'iStatus' => 1,
                'iRole' => 0,
                'YearWouldGraduateIn' => 2021,
                'iYearStart' => 2018,
                'iYearEnd' => 2021,
                'dCotisationValidUntil' => date('Y-m-d', strtotime('+1 year')),
            ],
            [
                'sLogin' => 'jane_smith',
                'sPassword' => password_hash('password123', PASSWORD_BCRYPT),
                'FirstName' => 'Jane',
                'LastName' => 'Smith',
                'iStatus' => 1,
                'iRole' => 0,
                'YearWouldGraduateIn' => 2019,
                'iYearStart' => 2016,
                'iYearEnd' => 2019,
                'dCotisationValidUntil' => date('Y-m-d', strtotime('-1 month')),
            ],
        ];

        foreach ($users as $user) {
            $u = R::dispense('users');
            $u->sLogin = $user['sLogin'];
            $u->sPassword = $user['sPassword'];
            $u->FirstName = $user['FirstName'];
            $u->LastName = $user['LastName'];
            $u->iStatus = $user['iStatus'];
            $u->iRole = $user['iRole'];
            $u->YearWouldGraduateIn = $user['YearWouldGraduateIn'];
            $u->iYearStart = $user['iYearStart'];
            $u->iYearEnd = $user['iYearEnd'];
            $u->dCotisationValidUntil = $user['dCotisationValidUntil'];
            $u->dCreation = date('Y-m-d H:i:s');
            R::store($u);
        }

        $this->logger->info("Users loaded");
    }
}

// Run fixtures if executed directly
if (php_sapi_name() === 'cli' || php_sapi_name() === 'cli-server') {
    $fixtures = new LoadFixtures();
    $fixtures->loadAll();
    echo "✓ Tous les fixtures ont été chargés avec succès\n";
}
