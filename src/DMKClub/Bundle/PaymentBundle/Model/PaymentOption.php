<?php

namespace DMKClub\Bundle\PaymentBundle\Model;

class PaymentOption {
    const INTERNAL_ENUM_CODE = 'dmkclb_paymentoption';

    const SEPA_DIRECT_DEBIT = 'sepa_direct_debit';
    const BANKTRANSFER = 'banktransfer';
    const CASH = 'cash';
    const CREDITCARD = 'creditcard';
    const NONE = 'none';
}
