<?php
declare(strict_types=1);

namespace App\CodeKata\Domain;

class Justin
{
    public const DIR_UP = "up";
    public const DIR_DOWN = "down";
    public const DIR_LEFT = "left";
    public const DIR_RIGHT = "right";

    private Coordinate $coordinate;

    private string $direction;

    public function __construct(Coordinate $coordinate, string $direction)
    {
        if (!in_array($direction, [
            self::DIR_UP,
            self::DIR_DOWN,
            self::DIR_LEFT,
            self::DIR_RIGHT,
        ])) {
            throw new \InvalidArgumentException('Invalid direction ' . $direction . ' given');
        }
        $this->coordinate = $coordinate;
        $this->direction = $direction;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param Coordinate $coordinate
     * @return Action[]
     * @throws \Exception
     */
    public function moveTo(Coordinate $coordinate): array
    {
        switch (true) {
            case $coordinate->getX() > $this->coordinate->getX():
                $newDir = self::DIR_RIGHT;
                break;
            case $coordinate->getX() < $this->coordinate->getX():
                $newDir = self::DIR_LEFT;
                break;
            case $coordinate->getY() > $this->coordinate->getY():
                $newDir = self::DIR_DOWN;
                break;
            case $coordinate->getY() < $this->coordinate->getY():
                $newDir = self::DIR_UP;
                break;
            default:
                throw new \Exception('Should not be possible');
        }

        $oldDir = $this->direction;
        $this->direction = $newDir;
        $this->coordinate = $coordinate;

        if ($newDir === $oldDir) {
            return [new Action(Action::ACTION_MOVE)];
        }

        if (
            $oldDir === self::DIR_LEFT && $newDir === self::DIR_UP ||
            $oldDir === self::DIR_UP && $newDir === self::DIR_RIGHT ||
            $oldDir === self::DIR_RIGHT && $newDir === self::DIR_DOWN ||
            $oldDir === self::DIR_DOWN && $newDir === self::DIR_LEFT
        ) {
            return [
                new Action(Action::ACTION_TURN_RIGHT),
                new Action(Action::ACTION_MOVE),
            ];
        }
        return [
            new Action(Action::ACTION_TURN_LEFT),
            new Action(Action::ACTION_MOVE),
        ];
    }

    /**
     * @param ParkingPlace $parkingPlace
     * @return Coordinate[]
     */
    public function getPossibleCoordinatesToGoTo(ParkingPlace $parkingPlace, Coordinate $destination): array
    {
        $neighbourCoordinates = [
            $this->coordinate->withX($this->coordinate->getX() - 1),
            $this->coordinate->withX($this->coordinate->getX() + 1),
            $this->coordinate->withY($this->coordinate->getY() - 1),
            $this->coordinate->withY($this->coordinate->getY() + 1),
        ];

        //Remove coordinate in Justin's back
        $frontLeftRightNeighbourCoordinates = $this->removeBackCoordinate($neighbourCoordinates);

        //Remove coordinates that are on the edge
        $coordinatesOnParkingPlace = array_filter(
            $frontLeftRightNeighbourCoordinates,
            fn (Coordinate $coordinate) => !$coordinate->isOutsideParkingPlace($parkingPlace) || $coordinate == $destination,
        );

        //Remove coordinates where parking place is found
        return array_filter($coordinatesOnParkingPlace, fn (Coordinate $coordinate) => !in_array($coordinate, $parkingPlace->getParkedCars(), false));
    }

    private function removeBackCoordinate(array $neighbourCoordinates): array
    {
        switch ($this->direction) {
            case self::DIR_UP:
                unset($neighbourCoordinates[3]);
                break;
            case self::DIR_DOWN:
                unset($neighbourCoordinates[2]);
                break;
            case self::DIR_LEFT:
                unset($neighbourCoordinates[1]);
                break;
            case self::DIR_RIGHT:
                unset($neighbourCoordinates[0]);
                break;
        }
        return $neighbourCoordinates;
    }

    public function teleportTo(self $ghostJustin): void
    {
        $this->coordinate = $ghostJustin->getCoordinate();
        $this->direction = $ghostJustin->getDirection();
    }

    public function turnAround(): Action
    {
        switch ($this->direction) {
            case self::DIR_UP:
                $this->direction = self::DIR_DOWN;
                break;
            case self::DIR_DOWN:
                $this->direction = self::DIR_UP;
                break;
            case self::DIR_LEFT:
                $this->direction = self::DIR_RIGHT;
                break;
            case self::DIR_RIGHT:
                $this->direction = self::DIR_LEFT;
                break;
        }
        return new Action(Action::ACTION_TURN_AROUND);
    }
}