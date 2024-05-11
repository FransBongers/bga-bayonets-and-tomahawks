<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Spaces;

class PathCalculator extends \APP_DbObject
{
  private $result;
  private $maxLevel;

  function __construct($maxLevel)
  {
    Notifications::log('GetPaths constructor',$maxLevel);
    $this->result = [];
    $this->maxLevel = $maxLevel;
  }

  function findAllPathsUtil($allSpaces, $set, $sourceId, $destinationId, $visited, $localPathList)
  {
    // Notifications::log('findAllPathsUtil', [
    //   'sourceId' => $sourceId,
    //   'destinationId' => $destinationId,
    //   'localPathList' => $localPathList,
    // ]);
    if ($sourceId === $destinationId) {
      // Notifications::log('path',$localPathList);
      // Todo: check if necessary to clone?
      $clone = [];
      foreach($localPathList as $spaceId) {
        $clone[] = $spaceId;
      }
      $this->result[] = $clone;
      return;
    }

    // Plus one because the source is also in the path
    if (count($localPathList) === $this->maxLevel + 1) {
      return;
    }

    $visited[$sourceId] = true;

    $adjacentSpaceIds = $allSpaces[$sourceId]->getAdjacentSpacesIds();

    foreach($adjacentSpaceIds as $spaceId) {
      if (!in_array($spaceId, $set) || $visited[$spaceId]) {
        continue;
      };
      $localPathList[] = $spaceId;
      $this->findAllPathsUtil($allSpaces, $set, $spaceId, $destinationId, $visited, $localPathList);

      $index = Utils::array_find_index($localPathList, function ($localPathListId) use ($spaceId) {
        return $localPathListId === $spaceId;
      });
      unset($localPathList[$index]);
      $localPathList = array_values($localPathList);
    }

    $visited[$sourceId] = false;
  }


  public function findAllPathsBetweenSpaces($sourceId, $destinationId, $set)
  {
    Notifications::log('findAllPathsBetweenSpaces', [
      'sourceId' => $sourceId,
      'destinationId' => $destinationId,
      'set' => $set,
      'maxLevel' => $this->maxLevel,
    ]);

    $allSpaces = Spaces::getAll();

    $visited = [];

    foreach($set as $spaceId) {
      $visited[$spaceId] = false;
    }

    $pathList = [$sourceId];

    $this->findAllPathsUtil($allSpaces, $set, $sourceId, $destinationId, $visited, $pathList);
    // function findPaths($paths, $path, $parent, $n, $u) {
    //   // Base case
    //   if ($u === -1) {
        
    //   }
    // }
      return $this->result;
  }
}
