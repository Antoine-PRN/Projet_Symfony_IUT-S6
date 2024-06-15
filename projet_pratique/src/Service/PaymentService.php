<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Charge;
use App\Entity\Event;

class PaymentService
{
  private $stripeSecretKey;
  private $stripePublicKey;

  public function __construct(string $stripeSecretKey, string $stripePublicKey)
  {
    $this->stripeSecretKey = $stripeSecretKey;
    $this->stripePublicKey = $stripePublicKey;
  }

  public function processPayment($amount, $token)
  {
    \Stripe\Stripe::setApiKey($this->stripeSecretKey);

    return \Stripe\Charge::create([
      'amount' => $amount,
      'currency' => 'eur',
      'source' => $token,
    ]);
  }
}
