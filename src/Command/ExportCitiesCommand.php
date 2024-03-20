<?php

namespace App\Command;

use App\Service\ExportCitiesService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:export-cities')] 
// nom de la commande à taper pour enregistrer dans la base de données les données
class ExportCitiesCommand extends Command
{
	public function __construct (private ExportCitiesService $exportCitiesService)
	{
		parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
	{
		ini_set('memory_limit', '2048M');
		$io = new SymfonyStyle($input, $output); // mise en page dans la ligne de commande

		$this->exportCitiesService->exportCities($io);
		$this->exportCitiesService->exportCitiesJson($io);
    
		return Command::SUCCESS;
	}
}
