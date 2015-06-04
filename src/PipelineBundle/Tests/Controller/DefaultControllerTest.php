<?php

namespace PipelineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
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

    public function testDown() {
      $client = static::createClient();
      $client->request('GET', '/tracker/bananas/3/down');

      # when no count, default to zero
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "name" => "bananas",
          "count" => -3,
        )
      );

      $client = static::createClient();
      $client->request('GET', '/tracker/bananas/2/down');

      # when already have a count, modify it.
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "name" => "bananas",
          "count" => -5,
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
      $client->request('GET', '/app/tracker/bananas/bar/up');

      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => false,
          "error" => "invalid count bar",
        )
      );
    }

    public function testEvents() {
      # TODO
    }

    # TODO:
    #   Test goofy encodings
    #   Test long string names
    #   Test large counts
    #   Test negative counts
}
