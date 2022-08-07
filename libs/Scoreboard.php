<?php

namespace learxd\pit\libs\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

class Scoreboard
{

    private ?Player $player;

    private string $objective;
    private string $displayName;

    private array $lines = [];

    private bool $hasSent = false;

    private ?SetDisplayObjectivePacket $objectivePacket;
    private ?RemoveObjectivePacket $removePacket;

    const SPACE = ' ';

    public function __construct(Player $player, string $objective, string $displayName = "") {
        $this->player = $player;

        $this->objective = $objective;
        $this->displayName = $displayName;

        $this->init();
    }

    public function init(): void
    {
        $this->objectivePacket = new SetDisplayObjectivePacket();
        $this->objectivePacket->displaySlot = 'sidebar';
        $this->objectivePacket->objectiveName = $this->objective;
        $this->objectivePacket->displayName = $this->displayName;
        $this->objectivePacket->criteriaName = 'dummy';
        $this->objectivePacket->sortOrder = 0;

        $this->removePacket = new RemoveObjectivePacket();
        $this->removePacket->objectiveName = $this->objective;

        $this->send();
    }

    public function send(): bool {
        if(!$this->hasSent) {
            $this->getPlayer()->getNetworkSession()->sendDataPacket($this->objectivePacket);
        }
        return $this->hasSent = !$this->hasSent;
    }

    public function remove(): bool {
        if($this->hasSent) {
            $this->getPlayer()->getNetworkSession()->sendDataPacket($this->removePacket);
        }
        return $this->hasSent = !$this->hasSent;
    }

    public function getPlayer(): ?Player {
        return $this->player;
    }

    public function getLine(int $line): ?ScorePacketEntry {
        return $this->lines[$line];
    }

    public function setLines(array $lines): int {
        $success = 0;
        foreach ($lines as $id => $entry) {
            $this->setLine($id, $entry) and $success++;
        }
        return $success;
    }

    public function setLine(int $id, string $line = ""): bool {

        if($id < 1 or $id > 15)
            return false;

        if(isset($this->lines[$id])) {
            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_REMOVE;
            $pk->entries[] = $this->getLine($id);

            $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
        }

        $entry = new ScorePacketEntry();
        $entry->objectiveName = $this->objective;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $line . str_repeat(self::SPACE, $id); // #blamemojang
        $entry->score = $id;
        $entry->scoreboardId = $id;

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;

        $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
        $this->lines[$id] = $entry;
        return true;
    }

}