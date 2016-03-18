<?php


namespace BlueSteel42\SettingsBundle\Tests\Adapter;

use BlueSteel42\SettingsBundle\Tests\TestCase;

abstract class BaseAdapterTester extends TestCase
{
    protected $env;

    /**
     * @var AppKernel
     */
    protected $kernel;

    /**
     * @var AdapterInterface
     */
    protected $service;

    public function setUp()
    {
        parent::setUp();
        $this->kernel = $this->getKernel($this->env);
        $this->service = $this->kernel->getContainer()->get('bluesteel42.settings');
    }

    public function testSetAndGet()
    {
        $this->service->set('foo', 'bar');
        $this->assertEquals($this->service->get('foo'), 'bar');
    }

    public function testFlush()
    {
        $t = $this->service->flush();
        $this->assertInstanceOf('BlueSteel42\SettingsBundle\Service\Settings', $t);
    }

    public function testGetException()
    {
        $this->setExpectedException('Symfony\Component\PropertyAccess\Exception\NoSuchIndexException');
        $this->service->get('unknown_key');
    }

    public function testDelete()
    {
        $this->service->setAll(array('foo' => 'foo', 'bar' => 'bar'));
        $this->service->delete('bar');

        $this->setExpectedException('Symfony\Component\PropertyAccess\Exception\NoSuchIndexException');
        $this->service->get('bar');
    }
}