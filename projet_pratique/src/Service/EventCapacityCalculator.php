<?php

namespace App\Service;

use App\Entity\Event;

class EventCapacityCalculator
{
  public function calculateRemainingPlaces(Event $event): int
  {
    $maxParticipants = $event->getMaxParticipants();
    $currentParticipants = count($event->getParticipants());

    return max(0, $maxParticipants - $currentParticipants);
  }
}
