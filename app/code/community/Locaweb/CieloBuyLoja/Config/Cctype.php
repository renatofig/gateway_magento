<?php
class Locaweb_CieloBuyLoja_Config_Cctype extends Locaweb_Abstract_Config_Cctype
{
  // Ignoring cctypes. Magento doesn't handle in xml to add and to ignore. :\
  public function ignore_cc_types() {
    return array("JCB", "SM", "SO", "OT", "AE");
  }
}
