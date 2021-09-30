<?php
declare(strict_types=1);

namespace App\CodeKata\Repository;

use App\CodeKata\Domain\Coordinate;
use App\CodeKata\Domain\Justin;
use App\CodeKata\Domain\ParkingPlace;

interface ParkingPlaceRepository
{
    public function getParkingPlace(): ParkingPlace;

    public function getJustin(string $initialDirection): Justin;

    public function getBusLocation(): Coordinate;

    public function getExit(): Coordinate;
}