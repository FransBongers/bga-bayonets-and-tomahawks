<?php

namespace BayonetsAndTomahawks\Models\Connections;


class Path extends \BayonetsAndTomahawks\Models\Connection
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = PATH;
    $this->limit = 4;
  }

  // .##.....##.########.####.##.......####.########.##....##
  // .##.....##....##.....##..##........##.....##.....##..##.
  // .##.....##....##.....##..##........##.....##......####..
  // .##.....##....##.....##..##........##.....##.......##...
  // .##.....##....##.....##..##........##.....##.......##...
  // .##.....##....##.....##..##........##.....##.......##...
  // ..#######.....##....####.########.####....##.......##...


}
