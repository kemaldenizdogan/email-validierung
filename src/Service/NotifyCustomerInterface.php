<?php

namespace App\Service;

use App\Entity\Customer;

interface NotifyCustomerInterface
{
    public function sendEmail(Customer $customer): bool;
}
