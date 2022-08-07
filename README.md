# ScoreboardAPI
An implementation of PocketMine to facilitate the creation of Scoreboards

## Usage

````
// CREATE WINDOW

ScoreboardManager::sendScoreboard($player,'teste','Teste', 
[
  1 => "Line 1",
  2 => "Empty Line",
  15 => "Line 15",
]
);


// GET/REMOVE WINDOW

/** @var Player $player */
if($scoreboard = ScoreboardManager::getPlayerScoreboard($player) {
  $scoreboard->remove(); // TO REMOVE
}

// CHANGE LINE(s)
if($scoreboard = ScoreboardManager::getPlayerScoreboard($sender)) {
  $scoreboard->setLine(1, 'Line 1');
  $scoreboard->setLines([
    2 => "Line 2",
    3 => "Line 3"
  ]);
}
````
