<?php
declare(strict_types=1);

namespace App\CodeKata\Repository;

use App\CodeKata\Domain\Coordinate;
use App\CodeKata\Domain\Justin;
use App\CodeKata\Domain\ParkingPlace;
use function Arrayy\array_first;

class TXTParkingPlaceRepository implements ParkingPlaceRepository
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getParkingPlace(): ParkingPlace
    {
        $structure = $this->getStructure();
        $maxX = count($structure) - 1;
        $maxY = count($structure[0]) - 1;

        $parkedCars = $this->getCoordinates("A");

        return new ParkingPlace($maxX, $maxY, $parkedCars);
    }

    /**
     * @return Coordinate[]
     */
    private function getCoordinates(string $neededValue): array
    {
        $structure = $this->getStructure();
        $coordinates = [];
        foreach ($structure as $x => $col) {
            foreach ($col as $y => $value) {
                if ($neededValue === $value) {
                    $coordinates[] = new Coordinate($x, $y);
                }
            }
        }
        return $coordinates;
    }

    public function getJustin(string $initialDirection): Justin
    {
        $coordinates = $this->getCoordinates("J");
        if (count($coordinates) !== 1) {
            throw new \RuntimeException("No Justin found (or multiple Justins found)");
        }

        return new Justin(array_first($coordinates), $initialDirection);
    }

    public function getBusLocation(): Coordinate
    {
        $coordinates = $this->getCoordinates("B");
        if (count($coordinates) !== 1) {
            throw new \RuntimeException("No bus found (or multiple cars found)");
        }
        return array_first($coordinates);
    }

    public function getExit(): Coordinate
    {
        $edge = $this->getCoordinates("X");

        $structure = $this->getStructure();
        $maxX = count($structure) - 1;
        $maxY = count($structure[0]) - 1;

        $exits = [];
        foreach ($structure as $x => $col) {
            foreach ($col as $y => $value) {
                if ($value === "X" || $value === "J") {
                    continue;
                }
                if ($y !== 0 && $y !== $maxY && $x !== 0 && $x !== $maxX) {
                    continue;//No border
                }
                $exits[] = new Coordinate($x, $y);
            }
        }
        if (count($exits) !== 1) {
            throw new \RuntimeException("No exit found (or multiple exit found)");
        }
        return array_first($exits);
    }

    private function getStructure(): array
    {

        $structure = [];
        $lines = explode(PHP_EOL, $this->content);
        foreach ($lines as $y => $line) {
            foreach (str_split(trim($line)) as $x => $value) {
                $structure[$x][$y] = $value;
            }
        }

        return $structure;
    }
}