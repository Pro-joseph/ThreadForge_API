<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(
    Tests\TestCase::class,
    LazilyRefreshDatabase::class,
)->in('Feature');
