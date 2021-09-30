<?php
declare(strict_types=1);

namespace App\CodeKata\Domain;

class Action
{
    public const ACTION_MOVE = "V";
    public const ACTION_TURN_LEFT = "L";
    public const ACTION_TURN_RIGHT = "R";
    public const ACTION_TURN_AROUND = "M";

    private string $action;

    public function __construct(string $action)
    {
        if (!in_array($action, [
            self::ACTION_MOVE,
            self::ACTION_TURN_LEFT,
            self::ACTION_TURN_RIGHT,
            self::ACTION_TURN_AROUND,
        ], true)) {
            throw new \InvalidArgumentException("Invalid action given");
        }
        $this->action = $action;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}