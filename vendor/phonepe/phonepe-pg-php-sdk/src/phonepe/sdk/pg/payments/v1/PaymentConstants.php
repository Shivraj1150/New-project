<?php

namespace PhonePe\payments\v1;

class PaymentConstants
{
    const REFUND_API = "/pg/v1/refund";
    const STATUS_API = "/pg/v1/status/%s/%s";
    const PAY_API = "/pg/v1/pay";
    const VALIDATE_VPA_API = "/pg/v1/vpa/validate";
    const PAYMENT_OPTIONS_API = "/pg/v1/options/%s";
    const NETBANKING_INCLUDE_LIST = "includeNetBankingBanksList";
}