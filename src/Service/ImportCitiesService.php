<?php

namespace App\Service;

use App\Entity\City;
use App\Entity\Department;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use League\Csv\Reader;

class ImportCitiesService
{
	public function __construct(
		private CityRepository $cityRepo,
		private EntityManagerInterface $em
	) {
	}

	public function importCities(SymfonyStyle $io): void
	{
		$io->title('Importation des villes');

		$cities = $this->readCsvFile(); // stocke toutes les villes

		$io->progressStart(count($cities)); // permet d'avoir une barre de progression dans le terminal

		foreach ($cities as $arrayCity) {

			// create or update city
			$city = $this->createOrUpdateCity($arrayCity);
			$this->em->persist($city);

			$this->em->flush();
			// forward indicator
			$io->progressAdvance();
		}


		$io->progressFinish();
		$io->success('L\'importation est terminée');
	}

	private function readCsvFile(): Reader
	{
		$csv = Reader::createFromPath('%kernel.root.dir%/../import/cities.csv', 'r'); // class Reader (librairy : composer require	league/csv / kernel = repertoire root du projet ; mode Read
		$csv->setHeaderOffset(0); // header = ligne 0 du fichier

		return $csv;
	}


	private function createOrUpdateCity(array $arrayCity): City
	{
		$city = $this->cityRepo->findOneBy(['inseeCode' => $arrayCity['insee_code']]);
		if (!$city) {
			$city = new City();
		}

		$city
			->setInseeCode($arrayCity['insee_code'])
			->setCityCode($arrayCity['city_code'])
			->setZipCode($arrayCity['zip_code'])
			->setLabel($arrayCity['label'])
			->setLatitude($arrayCity['latitude'])
			->setLongitude($arrayCity['longitude'])
			->setDepartmentName($arrayCity['department_name'])
			->setDepartmentNumber($arrayCity['department_number'])
			->setRegionName($arrayCity['region_name'])
			->setRegionGeoJsonName($arrayCity['region_geojson_name']);

		return $city;
	}
}
