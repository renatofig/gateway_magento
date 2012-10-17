<?php

class Locaweb_RedeCardWS_Model_Transaction extends Locaweb_Abstract_Model_Payment_Method_Cc
{
  protected $_code = 'redecardws';
  protected $_formBlockType = 'redecardws/form_cc';
  protected $_canSaveCc = false;
  protected $_canCapture = true;

  public function capture(Varien_Object $payment, $amount)
  {
    $order       = $payment->getOrder();
    $params      = $this->prepareParams($order, $payment, $amount);
    $transaction = $this->createTransaction($params);

    if($transaction->erro) {
      Mage::throwException($this->_getHelper()->__('Payment capturing error.'));
    }

    if($this->_is_paid($transaction)) {
      $payment->setTransactionId($transaction->id);
      $payment->setIsTransactionClosed(0);

      $payment->setTransactionAdditionalInfo('raw_details_info',
          array(
            'Status no Gateway'           => $transaction->status,
            'ID no Gateway'               => $transaction->id,
            'Numero Sequencial'           => $transaction->detalhes->numero_sequencial,
            'Numero Comprovante de Venda' => $transaction->detalhes->numero_comprovante_venda,
            'Numero Sequencial'           => $transaction->detalhes->numero_autenticacao,
            'Numero de Autorizacao'       => $transaction->detalhes->numero_autorizacao,
            'URI comprovante'             => $transaction->detalhes->url_comprovante,
            'Meio de Pagamento'           => $this->_payment_service_name()
          )
      );
    } else {
      Mage::throwException($this->_getHelper()->__('Payment Denied.'));
    }
    $order->save();

    return $this;
  }

  protected function _payment_service_name() {
    return 'redecard_ws';
  }
}
?>
