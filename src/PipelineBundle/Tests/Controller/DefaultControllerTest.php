<?php

namespace PipelineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testUp() {
      $client = static::createClient();
      $client->request('GET', '/app/tracker/bananas/3/up');

      # when no value, default to zero
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "key" => "bananas",
          "value" => 3,
        )
      );

      $client->request('GET', '/app/tracker/bananas/2/up');

      # when already have a value, modify it.
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "key" => "bananas",
          "value" => 5,
        )
      );
    }

    public function testDown() {
      $client = static::createClient();
      $client->request('GET', '/app/tracker/bananas/3/down');

      # when no value, default to zero
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "key" => "bananas",
          "value" => -3,
        )
      );

      $client->request('GET', '/app/tracker/bananas/2/down');

      # when already have a value, modify it.
      $this->assertEquals(
        json_decode($client->getResponse()->getContent(), true),
        array(
          "success" => true,
          "key" => "bananas",
          "value" => -5,
        )
      );
    }

    public function testInvalidDirection() {
      $client = static::createClient();
      $client->request('GET', '/app/tracker/bananas/3/foo');
      # when no value, default to zero
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
      # when no value, default to zero
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

}
