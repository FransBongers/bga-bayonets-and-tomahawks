<?php
namespace BayonetsAndTomahawks\Spaces;

class CoteDeBeaupre extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE;
    $this->battlePriority = 61;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 2;
    $this->name = clienttranslate('Côte de Beaupré');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 734;
    $this->left = 360.5;
    $this->adjacentSpaces = [
      COTE_DU_SUD => COTE_DE_BEAUPRE_COTE_DU_SUD,
      JACQUES_CARTIER => COTE_DE_BEAUPRE_JACQUES_CARTIER,
      QUEBEC => COTE_DE_BEAUPRE_QUEBEC,
      TADOUSSAC => COTE_DE_BEAUPRE_TADOUSSAC,
    ];
  }
}
