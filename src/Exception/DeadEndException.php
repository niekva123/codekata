<?php
declare(strict_types=1);

namespace App\CodeKata\Exception;

class DeadEndException extends \DomainException
{
    public static function make(): self
    {
        return new self("Too bad, dead end");
    }
}