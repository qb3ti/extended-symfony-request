<?php

namespace Qb3ti\ExtendedSymfonyRequest\Tests;

use PHPUnit\Framework\TestCase;
use Qb3ti\ExtendedSymfonyRequest\Request;

class RequestTest extends TestCase
{
    public function testLoadParametersFromQuery()
    {
        Request::setUrlPatterns([
            "/path/test/{{id}}/{{name}}"
        ]);

        $request = Request::create("/path/test/1/foo");

        $this->assertEquals(
            [
                $request->query->get("id"),
                $request->query->get("name")
            ], [
                1,
                "foo"
            ]
        );
    }
}