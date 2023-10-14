<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Notifications;


trait DebugTrait
{
  function test()
  {
    Notifications::log('test',[]);
  }

}
