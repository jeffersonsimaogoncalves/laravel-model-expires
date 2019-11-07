<?php

namespace Mvdnbrk\ModelExpires\Tests;

use Illuminate\Support\Carbon;
use Mvdnbrk\ModelExpires\Tests\Models\CustomSubscription;
use Mvdnbrk\ModelExpires\Tests\Models\Subscription;

class ExpiresTest extends TestCase
{
    /** @test */
    public function it_has_a_expires_at_column_with_value_null()
    {
        $model = Subscription::create();

        $this->assertNull($model->fresh()->expires_at);
    }

    /** @test */
    public function it_adds_the_expires_at_column_to_date_casts()
    {
        $model = Subscription::make();

        $this->assertContains('expires_at', $model->getDates());
    }

    /** @test */
    public function it_can_determine_the_expires_at_column()
    {
        $model = Subscription::make();

        $this->assertEquals('expires_at', $model->getExpiresAtColumn());
    }

    /** @test */
    public function it_can_customize_the_expires_at_column()
    {
        $model = CustomSubscription::make();

        $this->assertEquals('finishes_at', $model->getExpiresAtColumn());
    }

    /** @test */
    public function it_can_set_the_expires_at_column()
    {
        Carbon::setTestNow('2019-11-11 11:11:11');

        $model = Subscription::make([
            'expires_at' => Carbon::now()->addYear(),
         ]);

        $this->assertTrue($model->expires_at->equalTo('2020-11-11 11:11:11'));
    }

    /** @test */
    public function it_can_set_the_expires_at_column_with_an_integer()
    {
        Carbon::setTestNow('2019-11-11 11:11:11');

        $model = Subscription::make([
            'expires_at' => 60,
         ]);

        $this->assertTrue($model->expires_at->equalTo('2019-11-11 11:12:11'));
    }

    /** @test */
    public function it_unsets_the_expires_at_column_with_a_date_in_the_past()
    {
        $model = Subscription::make([
            'expires_at' => Carbon::now()->subMinute(),
         ]);

        $this->assertNull($model->expires_at);
    }

    /** @test */
    public function it_can_determine_if_it_has_expired()
    {
        $model = Subscription::make([
            'expires_at' => null,
        ]);
        $this->assertFalse($model->expired());

        $model->expires_at = Carbon::now()->addMinute();
        $this->assertFalse($model->expired());

        Carbon::setTestNow(Carbon::now()->addDay());
        $this->assertTrue($model->expired());
    }
}
