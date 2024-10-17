<?php

namespace PhonePe\payments\v1\models\response\PaymentInstrument;

use PhonePe\payments\v1\constants\PaymentInstrumentConstants;
use PhonePe\payments\v1\models\response\PaymentInstrument;

class UpiCollectPaymentInstrument extends PaymentInstrument implements \JsonSerializable
{
    public function __construct()
    {
        parent::__construct(PaymentInstrumentConstants::UPI_COLLECT);
    }
}
