<?php

namespace BayonetsAndTomahawks\Models\Connections;


class Highway extends \BayonetsAndTomahawks\Models\Connection
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = HIGHWAY;
    $this->limit = 16;
  }

  // .##.....##.########.####.##.......####.########.##....##
  // .##.....##....##.....##..##........##.....##.....##..##.
  // .##.....##....##.....##..##........##.....##......####..
  // .##.....##....##.....##..##........##.....##.......##...
  // .##.....##....##.....##..##........##.....##.......##...
  // .##.....##....##.....##..##........##.....##.......##...
  // ..#######.....##....####.########.####....##.......##...


}