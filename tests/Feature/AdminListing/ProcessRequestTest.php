<?php

namespace Brackets\AdminListing\Tests\Feature\AdminListing;

use Brackets\AdminListing\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;

class ProcessRequestTest extends TestCase
{
    // TODO refactor this class - creates a fake route and remove all the request mocking

    public function testRequestProcessingWithNothingSent()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('id');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn(null);

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(1);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(10);

        $result = $this->listing
            ->processRequestAndGet($request, ['id', 'name', 'number'], ['name', 'color']);

        $this->assertCount(10, $result);
    }

    public function testRequestProcessingWithOrdering()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('published_at');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn(null);

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(1);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(10);

        $result = $this->listing
            ->processRequestAndGet($request, ['id', 'name', 'number'], ['name', 'color']);

        $this->assertCount(10, $result);
        $this->assertEquals('Zeta 2', $result->getCollection()->first()->name);
    }


    public function testRequestProcessingWithSearch()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('published_at');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn('yellow a 1');

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(1);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(10);

        $result = $this->listing
            ->processRequestAndGet($request, ['*'], ['name', 'color']);

        $this->assertCount(1, $result);
        $this->assertEquals('Zeta 10', $result->getCollection()->first()->name);
    }

    public function testRequestProcessingWithPaginationManipulated()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('published_at');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn(null);

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(2);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(4);

        $result = $this->listing
            ->processRequestAndGet($request, ['*'], ['name', 'color']);

        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(3, $result->lastPage());
        $this->assertEquals('Zeta 5', $result->getCollection()->first()->name);
    }


    public function testRequestProcessingOnTranslatableModelWithDefaultLocale()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('published_at');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn('a 1');

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(1);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(10);

        $result = $this->translatedListing
            ->processRequestAndGet($request, ['id', 'color'], ['id', 'name', 'color']);

        $this->assertEquals(2, $result->total());
        $this->assertEquals(null, $result->getCollection()->first()->name);
        $this->assertEquals('red', $result->getCollection()->first()->color);
    }

    public function testRequestProcessingOnTranslatableModelWithSkLocale()
    {
        self::markTestSkipped('Refactor needed');
        $request = Mockery::mock(Request::class);

        $request->shouldReceive('input')
            ->withArgs(['orderBy', 'id'])
            ->once()
            ->andReturn('published_at');

        $request->shouldReceive('input')
            ->withArgs(['orderDirection', 'asc'])
            ->once()
            ->andReturn('asc');

        $request->shouldReceive('input')
            ->withArgs(['search', null])
            ->once()
            ->andReturn('a 1');

        $request->shouldReceive('input')
            ->withArgs(['page', 1])
            ->once()
            ->andReturn(1);

        $request->shouldReceive('input')
            ->withArgs(['per_page', 10])
            ->once()
            ->andReturn(10);

        $result = $this->translatedListing
            ->processRequestAndGet($request, ['id', 'color'], ['id', 'name', 'color'], null, 'sk');

        $this->assertEquals(1, $result->total());
        $this->assertEquals(null, $result->getCollection()->first()->name);
        $this->assertEquals('cervena', $result->getCollection()->first()->color);
    }
}
