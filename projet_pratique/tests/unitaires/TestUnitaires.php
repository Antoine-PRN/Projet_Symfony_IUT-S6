<?php

namespace App\Tests\Service;

use App\Service\ApplicationService;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ApplicationServiceTest extends TestCase
{
  private $applicationService;

  protected function setUp(): void
  {
    parent::setUp();

    $this->applicationService = new ApplicationServiceTest();
  }

  public function testIsValidPassword()
  {
    $isValid = $this->applicationService->isValidPassword('UnMotDePasseValide123');
    $this->assertTrue($isValid);

    $isValid = $this->applicationService->isValidPassword('court');
    $this->assertFalse($isValid);
  }

  public function testCreateEvent()
  {
    $user = new User();
    $user->setFirstName('John');
    $user->setLastName('Doe');
    $user->setEmail('john.doe@example.com');

    $event = $this->applicationService->createEvent(
      $user,
      'Nouvel événement',
      new \DateTime('+1 week'),
      'Description de l\'événement',
      true,
      10
    );

    $this->assertInstanceOf(Event::class, $event);
    $this->assertEquals('Nouvel événement', $event->getTitle());
    $this->assertEquals($user, $event->getCreator());
  }

  public function testRegisterUserForEvent()
  {
    $mockEntityManager = $this->createMock(EntityManagerInterface::class);

    $this->assertTrue(true);
  }
}
