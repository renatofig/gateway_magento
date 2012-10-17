<?php
class Locaweb_CieloBuyLoja_Block_Form_Cc extends Mage_Payment_Block_Form_Ccsave
{
  protected function _construct()
  {
      parent::_construct();
      $this->setTemplate('abstract/form/cc.phtml');
  }
}
?>
