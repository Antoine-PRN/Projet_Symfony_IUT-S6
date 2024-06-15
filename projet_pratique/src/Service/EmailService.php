<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class EmailService
{
  private $client;

  public function __construct(string $mailjetApiKey, string $mailjetApiSecret)
  {
    $this->client = new Client($mailjetApiKey, $mailjetApiSecret, true, ['version' => 'v3.1']);
  }

  public function sendEmail(string $toEmail, string $subject, string $body)
  {
    $response = $this->client->post(Resources::$Email, [
      'body' => [
        'Messages' => [
          [
            'From' => [
              'Email' => "antoine.perrin21@outlook.fr",
              'Name' => "Participation à l'évènement"
            ],
            'To' => [
              [
                'Email' => $toEmail,
                'Name' => "Recipient Name"
              ]
            ],
            'Subject' => $subject,
            'TextPart' => $body,
            'HTMLPart' => "<h3>{$body}</h3>"
          ]
        ]
      ]
    ]);

    return $response;
  }
}
