<?php
declare(strict_types=1);

namespace App\CodeKata;

use App\CodeKata\Domain\Justin;
use App\CodeKata\Exception\DeadEndException;
use App\CodeKata\Repository\ParkingPlaceRepository;
use Illuminate\Console\Concerns\InteractsWithIO;

class App
{
    private ParkingPlaceRepository $repository;

    public function __construct(ParkingPlaceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param InteractsWithIO $output
     */
    public function run($output)
    {
        $parkingPlace = $this->repository->getParkingPlace();
        $justin = $this->repository->getJustin(Justin::DIR_RIGHT);
        $busLocation = $this->repository->getBusLocation();
        $exit = $this->repository->getExit();

        $actions = $parkingPlace->getRouteActionsToCoordinate($justin, $busLocation);
        $actionsToExit = $parkingPlace->getRouteActionsToCoordinate($justin, $exit);

        $allActions = array_merge($actions, $actionsToExit);

        $instruction = [];
        foreach ($allActions as $action) {
            $instruction[] = $action->getAction();
        }
        $output->info(implode('-', $instruction));
    }
}