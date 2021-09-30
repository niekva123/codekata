<?php
declare(strict_types=1);

namespace App\CodeKata\Domain;

class Coordinate
{
    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function shouldBeInTheParkingPlace(ParkingPlace $parkingPlace): void
    {
        if ($this->isOutsideParkingPlace($parkingPlace)) {
            throw new \DomainException("Coordinate x:".$this->x.", y:".$this->y." is outside parking place or on the edge");
        }
    }

    public function isOutsideParkingPlace(ParkingPlace $parkingPlace): bool
    {
        return $this->x >= $parkingPlace->getCoordinateWidthMax()
            || $this->y >= $parkingPlace->getCoordinateHeightMax()
            || $this->x <= 0
            || $this->y <= 0
        ;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function withX(int $x): self
    {
        $self = clone $this;
        $self->x = $x;
        return $self;
    }

    public function withY(int $y): self
    {
        $self = clone $this;
        $self->y = $y;
        return $self;
    }
}