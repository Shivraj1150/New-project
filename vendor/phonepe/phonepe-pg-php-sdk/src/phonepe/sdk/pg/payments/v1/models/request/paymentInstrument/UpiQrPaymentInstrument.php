<?php

namespace PhonePe\payments\v1\models\request\paymentInstrument;

use PhonePe\payments\v1\constants\PaymentInstrumentConstants;
use PhonePe\payments\v1\models\request\PaymentInstrument;

class UpiQrPaymentInstrument extends PaymentInstrument
{
    public function __construct()
    {
        parent::__construct(PaymentInstrumentConstants::UPI_QR);
    }
}