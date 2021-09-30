<?php
declare(strict_types=1);

namespace App\CodeKata\Domain;

use App\CodeKata\Exception\DeadEndException;
use App\CodeKata\Exception\TooLongPathException;
use function Arrayy\array_first;

class ParkingPlace
{
    private int $coordinateWidthMax;

    private int $coordinateHeightMax;

    /**
     * @var Coordinate[]
     */
    private array $parkedCars;

    /**
     * @param int $coordinateWidthMax
     * @param int $coordinateHeightMax
     * @param Coordinate[] $parkedCars
     * @param Coordinate $justin
     * @param Coordinate $exit
     * @param Coordinate $parkingPlaceBus
     */
    public function __construct(int $coordinateWidthMax, int $coordinateHeightMax, array $parkedCars)
    {
        $this->coordinateWidthMax = $coordinateWidthMax;
        $this->coordinateHeightMax = $coordinateHeightMax;
        $this->parkedCars = $parkedCars;
        foreach ($this->parkedCars as $parkedCar) {
            $parkedCar->shouldBeInTheParkingPlace($this);
        }
    }

    public function getCoordinateWidthMax(): int
    {
        return $this->coordinateWidthMax;
    }

    public function getCoordinateHeightMax(): int
    {
        return $this->coordinateHeightMax;
    }

    /**
     * @return Coordinate[]
     */
    public function getParkedCars(): array
    {
        return $this->parkedCars;
    }

    /**
     * @param Justin $justin
     * @param Coordinate $destination
     * @param array $previousActions
     * @return Action[]
     * @throws \Exception
     */
    public function getRouteActionsToCoordinate(Justin $justin, Coordinate $destination): array
    {
        try {
            return $this->calculateRouteActionsToCoordinate($justin, $destination);
        } catch (DeadEndException $e) {
            return $this->calculateRouteActionsToCoordinate($justin, $destination, [$justin->turnAround()]);
        }
    }

    /**
     * @param Justin $justin
     * @param Coordinate $destination
     * @param array $previousActions
     * @return Action[]
     * @throws \Exception
     */
    private function calculateRouteActionsToCoordinate(Justin $justin, Coordinate $destination, array $previousActions = []): array
    {
        if ($justin->getCoordinate() == $destination) {
            return $previousActions;
        }

        if (count($previousActions) > 250) {
            throw TooLongPathException::make();
        }

        $coordinates = $justin->getPossibleCoordinatesToGoTo($this, $destination);

        if (count($coordinates) === 0) {
            throw DeadEndException::make();
        }

        if (count($coordinates) === 1) {
            $newActions = $justin->moveTo(array_first($coordinates));
            return $this->calculateRouteActionsToCoordinate($justin, $destination, array_merge($previousActions, $newActions));
        }

        $shortestPath = null;
        $ghostThatReachesDestionation = null;
        foreach ($coordinates as $coordinate) {
            $ghostJustin = clone $justin;
            try {
                $actions = $ghostJustin->moveTo($coordinate);
                $neededActions = $this->calculateRouteActionsToCoordinate($ghostJustin, $destination, array_merge($previousActions, $actions));

                if ($shortestPath === null || count($shortestPath) > count($neededActions)) {
                    $shortestPath = $neededActions;
                    $ghostThatReachesDestionation = $ghostJustin;
                }
            } catch (DeadEndException | TooLongPathException $exception) {}
        }
        if ($shortestPath === null) {
            throw DeadEndException::make();
        }

        $justin->teleportTo($ghostThatReachesDestionation);

        return $shortestPath;
    }
}