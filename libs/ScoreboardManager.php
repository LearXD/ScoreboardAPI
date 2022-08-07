<?php

namespace learxd\pit\libs\scoreboard;

use pocketmine\player\Player;

class ScoreboardManager
{

    /** @var Scoreboard[] */
    public static array $scoreboards = [];

    public static function sendScoreboard(
        Player $player,
        string $objective,
        string $displayName = "",
        array $entries = []
    ): bool{

        if($oldScore = self::getPlayerScoreboard($player)){
            $oldScore->remove();
        }

        self::$scoreboards[$player->getName()] = new Scoreboard(
            $player,
            $objective,
            $displayName
        );

        if(sizeof($entries) > 0) {
            self::$scoreboards[$player->getName()]->setLines($entries);
        }

        return true;
    }

    public static function removeScoreboard(Player $player): bool {
        if($scoreboard = self::getPlayerScoreboard($player)) {
            $scoreboard->remove();
            return true;
        }
        return false;
    }

    public static function getPlayerScoreboard(Player $player): ?Scoreboard {
        return self::$scoreboards[$player->getName()] ?? null;
    }

}