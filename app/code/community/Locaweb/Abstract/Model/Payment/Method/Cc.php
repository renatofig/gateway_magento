<?php

require_once Mage::getBaseDir('lib').DS.'LocawebGateway'.DS.'LocawebGatewayProcessor.php';

class Locaweb_Abstract_Model_Payment_Method_Cc extends Mage_Payment_Model_Method_Cc
{
  const RESPONSE_CODE_PAID            = 'paga';
  const RESPONSE_CODE_DECLINED        = 'negada';
  const RESPONSE_CODE_WAITING_PAYMENT = 'aguardando_pagamento';
  const RESPONSE_CODE_CANCELLED       = 'cancelada';

  public function __construct()
  {
    $this->_setEnv($this->getConfigData('sandbox_environment'));
    LocawebGatewayConfig::setToken($this->getConfigData('token'));
  }

  // Don't validate DINERS and ELO credit cards.
  // If is other cctype calling validate on parent to validate others credit cards
  //
  public function validate() {
    $info = $this->getInfoInstance();
    if ($info->getCcType() == "DIN" || $info->getCcType() == "ELO") {
      return $this;
    } else {
      parent::validate();
    }

    return $this;
  }

  protected function prepareParams($order, $payment, $amount)
  {
    $billingaddress         = $order->getBillingAddress();
    $order_number           = $order->getIncrementId();
    $order_description      = $this->_formatDescription($order);
    $order_total            = $this->_formatAmount($amount);
    $payment_service_name   = $this->_payment_service_name();
    $payment_cc_type        = $this->_translateCcType($payment->getCcType());
    $payment_cc_number      = $payment->getCcNumber();
    $payment_cc_cvv         = $payment->getCcCid();
    $payment_cc_due_date    = $this->_formatCcExpiration($payment->getCcExpMonth(), $payment->getCcExpYear());
    $payment_parcels        = $this->getConfigData('parcel_number');
    $payment_operation_type = $this->getConfigData('parcel_type');
    $buyer_document         = $order->getCustomerTaxvat();
    $buyer_name             = $this->_formatCustomerName($billingaddress->getData('firstname'), $billingaddress->getData('lastname'));
    $buyer_address          = $billingaddress->street;
    $buyer_zipcode          = $billingaddress->postcode;
    $buyer_city             = $billingaddress->city;
    $buyer_state            = $billingaddress->region;
    $return_uri             = 'http://locaweb.com.br'; #FIXME: Para Cielo Buy Loja sem autenticacao e Redecard Web Service essa url de retorno não será chamada. Porém em versões futuras será chamada pelo Cielo buy Page Cielo.

    $params = array(
      'url_retorno' => $return_uri,
      'capturar'    => 'true',
      'pedido' => array(
        'numero'    => $order_number,
        'total'     => $order_total,
        'moeda'     => 'real',
        'descricao' => $order_description
      ),
      'pagamento' => array(
        'meio_pagamento'  => $payment_service_name,
        'bandeira'        => $payment_cc_type,
        'cartao_numero'   => $payment_cc_number,
        'cartao_cvv'      => $payment_cc_cvv,
        'cartao_validade' => $payment_cc_due_date,
        'parcelas'        => $payment_parcels,
        'tipo_operacao'   => $payment_operation_type
      ),
      'comprador' => array(
        'nome'      => $buyer_name,
        'documento' => $buyer_document,
        'endereco'  => $buyer_address,
        'cep'       => $buyer_zipcode,
        'cidade'    => $buyer_city,
        'estado'    => $buyer_state
      )
    );

    return $params;
  }

  protected function createTransaction($params)
  {
    Mage::log("Enviando Dados Para o Novo Gateway de Pagamento:");
    $filter_params = $params;
    $filter_params["pagamento"]["cartao_numero"]   = "[FILTERED]";
    $filter_params["pagamento"]["cartao_cvv"]      = "[FILTERED]";
    $filter_params["pagamento"]["cartao_validade"] = "[FILTERED]";
    Mage::log($filter_params);

    try {
      $result = LocawebGateway::criar($params)->sendRequest();
    } catch(Exception $e) {
      Mage::log($e->getMessage());
      Mage::throwException($this->_getHelper()->__('An error happend during the connection to the Gateway.'));
    }


    Mage::log("Resposta do Novo Gateway de Pagamento:");
    Mage::log($result);

    return $result->transacao;
  }

  protected function _setEnv($config)
  {
    if($config) {
      LocawebGatewayConfig::setEnvironment('sandbox');
    }
  }

  protected function _is_paid($transaction) {
    return $transaction->status == self::RESPONSE_CODE_PAID;
  }

  protected function _translateCcType($type)
  {
    # TODO: Pegar somente os CC types habilitados para este meio de pagamento
    $types = Mage::getSingleton('payment/config')->getCcTypes();
    return strtolower($types[$type]);
  }

  protected function _formatCcExpiration($month, $year)
  {
    return sprintf('%02d', $month) . $year;
  }

  protected function _formatCustomerName($first_name, $last_name)
  {
    return $first_name . $last_name;
  }

  protected function _formatAmount($amount)
  {
    return number_format($amount, 2, '.', '');
  }

  protected function _formatDescription($order)
  {
    $order_number = $order->getIncrementId();
    $store_name   = $order->getStoreName(2); // See Mage_Sales_Model_Order#getStoreGroupName for more information about it.

    return 'Referente ao pedido ' . $order_number . '. Compra realizada na loja virtual ' . $store_name;
  }
}
?>
