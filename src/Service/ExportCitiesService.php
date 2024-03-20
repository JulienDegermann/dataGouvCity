<?php

namespace App\Service;

use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ExportCitiesService
{
  public function __construct(
    private CityRepository $cityRepo,
    private EntityManagerInterface $em
  ) {
  }


  public function exportCities(SymfonyStyle $io): void
  {
    $cities = $this->cityRepo->findAll();

    $io->title('Exportation des villes');
    $io->progressStart(count($cities));

    // find a folder to store the file
    $csvFile = fopen('export/citiesExport.csv', 'w');

    fputcsv($csvFile, ['insee_code', 'city_code', 'zip_code', 'label', 'latitude', 'longitude', 'department_name', 'department_number', 'region_name', 'region_geojson_name']);

    foreach ($cities as $city) {
      fputcsv($csvFile, [
        $city->getInseeCode(),
        $city->getCityCode(),
        $city->getZipCode(),
        $city->getLabel(),
        $city->getLatitude(),
        $city->getLongitude(),
        $city->getDepartmentName(),
        $city->getDepartmentNumber(),
        $city->getRegionName(),
        $city->getRegionGeojsonName()
      ]);


      $io->progressAdvance();
    }

    $io->progressFinish();

    $io->success('L\'exportation en CSV est terminée');
  }

  public function exportCitiesJson(SymfonyStyle $io): void
  {
    $cities = $this->cityRepo->findAll();

    $io->title('Exportation des villes');
    $io->progressStart(count($cities));


    $citiesData = [];
    foreach ($cities as $city) {
      $citiesData[] = [
        'insee_code' => $city->getInseeCode(),
        'city_code' => $city->getCityCode(),
        'zip_code' => $city->getZipCode(),
        'label' => $city->getLabel(),
        'latitude' => $city->getLatitude(),
        'longitude' => $city->getLongitude(),
        'department_name' => $city->getDepartmentName(),
        'department_number' => $city->getDepartmentNumber(),
        'region_name' => $city->getRegionName(),
        'region_geojson_name' => $city->getRegionGeojsonName()
      ];

      $io->progressAdvance();
    }


    $jsonData = json_encode($citiesData, JSON_PRETTY_PRINT);
    file_put_contents('export/citiesExport.json', $jsonData);

    $io->progressFinish();
    $io->success('L\'exportation en JSON est terminée');
  }
}
