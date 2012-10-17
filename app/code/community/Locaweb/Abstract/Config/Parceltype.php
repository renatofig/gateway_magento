<?php
class Locaweb_Abstract_Config_Parceltype
{
    public function toOptionArray() {
      return array(
          array('value' => 'credito_a_vista',          'label' => Mage::helper('adminhtml')->__('Crédito à Vista')),
          array('value' => 'parcelado_loja',           'label' => Mage::helper('adminhtml')->__('Parcelado Loja')),
          array('value' => 'parcelado_administradora', 'label' => Mage::helper('adminhtml')->__('Parcelado Administradora')),
          array('value' => 'debito',                   'label' => Mage::helper('adminhtml')->__('Débito'))
      );
    }
}
