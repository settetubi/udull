<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations, DatabaseTransactions;

    protected $faker;

    /**
     * set up the test
     */
    public function setUp(): void
    {
        parent::setUp();
//        $this->faker = Faker::create(10);
        $this->seed();
    }

    /**
     * Reset
     */
    public function tearDown(): void
    {
//        $this->artisan('migrate:reset');
        parent::tearDown();
    }
}
