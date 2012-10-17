<?php

class Locaweb_CieloBuyLoja_Model_Transaction extends Locaweb_Abstract_Model_Payment_Method_Cc
{
  protected $_code = 'cielobuyloja';
  protected $_formBlockType = 'cielobuyloja/form_cc';
  protected $_canSaveCc = false;
  protected $_canCapture = true;

  public function capture(Varien_Object $payment, $amount)
  {
    $order       = $payment->getOrder();
    $params      = $this->prepareParams($order, $payment, $amount);
    $transaction = $this->createTransaction($params);

    if($transaction->erro) {
      Mage::throwException($this->_getHelper()->__('Payment capturing error.'));
    } else {
      if ($transaction->status == self::RESPONSE_CODE_WAITING_PAYMENT && $transaction->url_acesso) {
        $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, 'pending_payment', '', false);
        Mage::log("Mudando o Status do Pedido para: " . Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
      } else if ($transaction->status != self::RESPONSE_CODE_PAID) {
        Mage::log("Transação Negada");
        Mage::throwException($this->_getHelper()->__('Payment Denied.'));
      }

      $payment->setTransactionId($transaction->id);
      $payment->setIsTransactionClosed(0);

      $payment->setTransactionAdditionalInfo('raw_details_info',
          array(
            'Numero Pedido'     => $transaction->numero_pedido,
            'Tid'               => $transaction->detalhes->tid,
            'Nsu'               => $transaction->detalhes->nsu,
            'Pan'               => $transaction->detalhes->pan,
            'Arp'               => $transaction->detalhes->arp,
            'Lr'                => $transaction->detalhes->lr,
            'Status no Gateway' => $transaction->status,
            'ID no Gateway'     => $transaction->id,
            'url_acesso'        => $transaction->url_acesso,
            'Meio de Pagamento' => $this->_payment_service_name()
          )
      );
    }
    $order->save();

    return $this;
  }

  protected function _payment_service_name() {
    return 'cielo';
  }
}
?>
