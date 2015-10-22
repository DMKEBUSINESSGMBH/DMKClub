<?php

namespace DMKClub\Bundle\PaymentBundle\Model;

class PaymentOption {
    const SEPA_DIRECT_DEBIT = 'sepa_direct_debit';
    const BANKTRANSFER = 'banktransfer';
    const CASH = 'cash';
    const CREDITCARD = 'creditcard';
    const NONE = 'none';
}
