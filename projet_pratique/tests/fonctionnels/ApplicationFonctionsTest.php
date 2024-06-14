<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationTest extends WebTestCase
{
  private $client;
  private $entityManager;

  protected function setUp(): void
  {
    parent::setUp();

    $this->client = static::createClient();

    $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
  }

  public function testUserRegistration()
  {
    $crawler = $this->client->request('GET', '/register');
    $this->assertResponseIsSuccessful();

    $form = $crawler->filter('form[name=registration_form]')->form([
      'registration_form[firstName]' => 'John',
      'registration_form[lastName]' => 'Doe',
      'registration_form[email]' => 'john.doe@example.com',
      'registration_form[plainPassword][first]' => 'Password123',
      'registration_form[plainPassword][second]' => 'Password123',
    ]);

    $this->client->submit($form);
    $this->assertResponseRedirects('/'); 

    $this->client->followRedirect();
    $this->assertSelectorTextContains('.alert-success', 'Registration successful');
  }

  public function testCreateEvent()
  {
    $user = $this->createUser('testuser@example.com', 'Password123');

    $this->client->loginUser($user);

    $crawler = $this->client->request('GET', '/events/new');
    $this->assertResponseIsSuccessful();

    $form = $crawler->filter('form[name=event_form]')->form([
      'event_form[title]' => 'New Event',
      'event_form[date]' => '2024-06-30',
      'event_form[description]' => 'Description of the event',
    ]);

    $this->client->submit($form);
    $this->assertResponseRedirects('/events'); 

    $this->client->followRedirect();
    $this->assertSelectorTextContains('.alert-success', 'Event created successfully');
    $this->assertSelectorTextContains('.event-title', 'New Event');
  }

  public function testViewEvents()
  {
    $crawler = $this->client->request('GET', '/events');
    $this->assertResponseIsSuccessful();

    $this->assertGreaterThan(0, $crawler->filter('.event-item')->count());
  }

  protected function createUser($email, $password)
  {
    $user = new User();
    $user->setEmail($email);

    $plainPassword = $password;
    $encodedPassword = $this->client->getContainer()->get('security.password_encoder')
      ->encodePassword($user, $plainPassword);
    $user->setPassword($encodedPassword);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $user;
  }
}
