<?php namespace Orchestra\Model\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Model\Memory\UserMetaProvider;

class UserMetaProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var Illuminate\Model\Memory\Application
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Orchestra\Model\Memory\UserMetaRepository::initiate()
     * method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $handler = m::mock('\Orchestra\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn(array())
            ->shouldReceive('finish')->once()->andReturn(true);

        $stub = new UserMetaProvider($handler);

        $refl   = new \ReflectionObject($stub);
        $items  = $refl->getProperty('items');

        $items->setAccessible(true);

        $items->setValue($stub, array(
            'foo/user-1'    => '',
            'foobar/user-1' => 'foo',
            'foo/user-2'    => ':to-be-deleted:'
        ));

        $this->assertTrue($stub->finish());
    }

    /**
     * Test Orchestra\Model\Memory\UserMetaRepository::get() method.
     *
     * @test
     */
    public function testGetMethod()
    {
        $handler = m::mock('\Orchestra\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn(array())
            ->shouldReceive('retrieve')->once()->with('foo/user-1')->andReturn('foobar')
            ->shouldReceive('retrieve')->once()->with('foobar/user-1')->andReturnNull();

        $stub  = new UserMetaProvider($handler);

        $this->assertEquals('foobar', $stub->get('foo.1'));
        $this->assertEquals(null, $stub->get('foobar.1'));
    }

    /**
     * Test Orchestra\Model\Memory\UserMetaRepository::forget()
     * method.
     *
     * @test
     */
    public function testForgetMethod()
    {
        $handler = m::mock('\Orchestra\Model\Memory\UserMetaRepository');

        $handler->shouldReceive('initiate')->once()->andReturn(array());

        $stub  = new UserMetaProvider($handler);

        $refl  = new \ReflectionObject($stub);
        $items = $refl->getProperty('items');

        $items->setAccessible(true);

        $items->setValue($stub, array(
            'foo/user-1'   => 'foobar',
            'hello/user-1' => 'foobar',
        ));

        $this->assertEquals('foobar', $stub->get('foo.1'));
        $stub->forget('foo.1');
        $this->assertNull($stub->get('foo.1'));
    }
}