<?php
declare(strict_types=1);

namespace App\CodeKata\Exception;

class TooLongPathException extends \DomainException
{
    public static function make(): self
    {
        return new self("Justin is tired...");
    }
}