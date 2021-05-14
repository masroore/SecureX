<?php

uses(Tests\TestCase::class)->in('Feature');
uses(Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');
uses()->group('auth')->in('Feature\Auth');
