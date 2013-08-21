<?php

use XWiki\Http\Client;

class HttpClientTest extends PHPUnit_Framework_TestCase
{

    public function testRemoveSlash()
    {
        $client = new Client('http://test.ro');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/', $address['resource']);

        $client = new Client('http://test.ro/');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/', $address['resource']);

        $client = new Client('test.ro');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/', $address['resource']);

        $client = new Client('test.ro/');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/', $address['resource']);

        $client = new Client('test.ro/test');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/test', $address['resource']);

        $client = new Client('http://test.ro/test');
        $address = $this->extractAddressField($client);
        $this->assertEquals('test.ro', $address['domain']);
        $this->assertEquals('/test', $address['resource']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentExceptionNoString()
    {
        new Client('');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentExceptionEmptyString()
    {
        new Client('  ');
    }

    private function extractAddressField(Client $client)
    {
        $reflection = new ReflectionClass($client);
        $address = $reflection->getProperty('address');
        $address->setAccessible(true);
        return $address->getValue($client);
    }
}