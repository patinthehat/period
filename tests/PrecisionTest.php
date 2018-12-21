<?php

namespace Spatie\Period\Tests;

use DateTime;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use PHPUnit\Framework\TestCase;
use Spatie\Period\Exceptions\CannotComparePeriods;

class PrecisionTest extends TestCase
{
    /**
     * @test
     * @dataProvider roundingDates
     */
    public function dates_are_rounded_on_precision(
        int $precision,
        string $expectedStart,
        string $expectedEnd
    ) {
        $period = Period::make(
            '2018-02-05 11:11:11',
            '2018-03-05 11:11:11',
            $precision
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', $expectedStart),
            $period->getStart()
        );

        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', $expectedEnd),
            $period->getEnd()
        );
    }

    public function roundingDates(): array
    {
        return [
            [Precision::YEAR, '2018-01-01 00:00:00', '2018-01-01 00:00:00'],
            [Precision::MONTH, '2018-02-01 00:00:00', '2018-03-01 00:00:00'],
            [Precision::DAY, '2018-02-05 00:00:00', '2018-03-05 00:00:00'],
            [Precision::HOUR, '2018-02-05 11:00:00', '2018-03-05 11:00:00'],
            [Precision::MINUTE, '2018-02-05 11:11:00', '2018-03-05 11:11:00'],
            [Precision::SECOND, '2018-02-05 11:11:11', '2018-03-05 11:11:11'],
        ];
    }

    /** @test */
    public function comparing_two_periods_with_different_precision_is_not_allowed()
    {
        $a = Period::make('2018-01-01', '2018-01-01', Precision::MONTH);
        $b = Period::make('2018-01-01', '2018-01-01', Precision::DAY);

        $this->expectException(CannotComparePeriods::class);

        $a->overlapsWith($b);
    }

    /** @test */
    public function precision_with_seconds()
    {
        $a = Period::make('2018-01-01 00:00:15', '2018-01-01 00:00:15', Precision::SECOND);
        $b = Period::make('2018-01-01 00:00:15', '2018-01-01 00:00:15', Precision::SECOND);
        $c = Period::make('2018-01-01 00:00:16', '2018-01-01 00:00:16', Precision::SECOND);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }

    /** @test */
    public function precision_with_minutes()
    {
        $a = Period::make('2018-01-01 00:15:00', '2018-01-01 00:15:00', Precision::MINUTE);
        $b = Period::make('2018-01-01 00:15:00', '2018-01-01 00:15:00', Precision::MINUTE);
        $c = Period::make('2018-01-01 00:16:00', '2018-01-01 00:16:00', Precision::MINUTE);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }

    /** @test */
    public function precision_with_hours()
    {
        $a = Period::make('2018-01-01 15:00:00', '2018-01-01 15:00:00', Precision::HOUR);
        $b = Period::make('2018-01-01 15:00:00', '2018-01-01 15:00:00', Precision::HOUR);
        $c = Period::make('2018-01-01 16:00:00', '2018-01-01 16:00:00', Precision::HOUR);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }

    /** @test */
    public function precision_with_days()
    {
        $a = Period::make('2018-01-01', '2018-01-01', Precision::DAY);
        $b = Period::make('2018-01-01', '2018-01-01', Precision::DAY);
        $c = Period::make('2018-01-02', '2018-01-02', Precision::DAY);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }

    /** @test */
    public function precision_with_months()
    {
        $a = Period::make('2018-01-01', '2018-01-01', Precision::MONTH);
        $b = Period::make('2018-01-01', '2018-01-01', Precision::MONTH);
        $c = Period::make('2018-02-01', '2018-02-01', Precision::MONTH);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }

    /** @test */
    public function precision_with_years()
    {
        $a = Period::make('2018-01-01', '2018-01-01', Precision::YEAR);
        $b = Period::make('2018-01-01', '2018-01-01', Precision::YEAR);
        $c = Period::make('2019-01-01', '2019-01-01', Precision::YEAR);

        $this->assertTrue($a->overlapsWith($b));
        $this->assertFalse($a->overlapsWith($c));
    }
}
