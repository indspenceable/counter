<?php

namespace PipelineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PipelineBundle\PipelineUpdateEvent;


class DefaultControllerTest extends WebTestCase
{
  protected function setUp()
  {
    self::bootKernel();
    # TODO this is pretty ugly, but given that there doesn't seem to be a
    # trivial way to enable transactional tests, we'll live with this for now.
    $bananas = static::$kernel->getContainer()
        ->get('doctrine')
        ->getRepository('PipelineBundle:Thing')
        ->findOneByName("bananas");

    if (!!$bananas) {
      $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
      $em->remove($bananas);
      $em->flush();
    }
  }

  public function testUp() {
    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/3/up');

    # when no count, default to zero
    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => true,
        "name" => "bananas",
        "count" => 3,
      )
    );

    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/2/up');

    # when already have a count, modify it.
    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => true,
        "name" => "bananas",
        "count" => 5,
      )
    );
  }

  public function testNegativeCount() {
    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/-3/up');

    # when no count, default to zero
    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => true,
        "name" => "bananas",
        "count" => -3,
      )
    );
  }

  public function testDown() {
    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/4/down');

    # when no count, default to zero
    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => true,
        "name" => "bananas",
        "count" => -4,
      )
    );

    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/5/down');

    # when already have a count, modify it.
    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => true,
        "name" => "bananas",
        "count" => -9,
      )
    );
  }

  public function testInvalidDirection() {
    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/3/foo');

    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => false,
        "error" => "invalid direction foo",
      )
    );
  }

  public function testInvalidCount() {
    $client = static::createClient();
    $client->request('GET', '/tracker/bananas/bar/up');

    $this->assertEquals(
      json_decode($client->getResponse()->getContent(), true),
      array(
        "success" => false,
        "error" => "invalid count bar",
      )
    );
  }

  public function testEvents() {
    $client = static::createClient();

    $mockListener = $this
      ->getMockBuilder('stdClass')
      ->setMethods(array('trigger'))
      ->getMock();

    $dispatcher = $client
      ->getContainer()
      ->get('event_dispatcher');

    $dispatcher->addListener('pipeline.update', array($mockListener, 'trigger'));

    $mockListener
      ->expects($this->once())
      ->method('trigger')
      ->with($this->isInstanceOf('PipelineBundle\PipelineUpdateEvent'));



    $client->request('GET', '/tracker/bananas/3/up');
  }

  # TODO:
  #   Test goofy encodings
}
