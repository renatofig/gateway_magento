<?php
class Locaweb_Abstract_Config_Cctype
{
  // Why this doesn't work?
  //
  // class Locaweb_Abstract_Config_Cctype extends Mage_Payment_Model_Source_Cctype {
  //   protected $_allowedTypes = array('AE','VI','MC','DI','JCB','OT');
  // }
  //
  // Ignoring cctypes. Magento doesn't handle in xml to add and to ignore cctypes. :\
  //
  public function toOptionArray() {
    $options =  array();
    foreach (Mage::getSingleton('payment/config')->getCcTypes() as $code => $name) {
      if(in_array($code, $this->ignore_cc_types())) {
        continue;
      }
      $options[] = array('value' => $code, 'label' => $name);
    }
    return $options;
  }

}