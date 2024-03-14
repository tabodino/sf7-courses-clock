<?php

namespace App\Tests\Twig\Extension;

use App\Twig\Extension\RemainingDurationExtension;
use PHPUnit\Framework\TestCase;

class RemainingDurationExtensionTest extends TestCase
{
    private RemainingDurationExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new RemainingDurationExtension();
    }

    // Test format on negative value
    public function testFormatRemainingDurationOnNegativeValue(): void
    {
        $result = $this->extension->formatRemainingDuration(-10);

        $this->assertEquals('', $result);
    }

    // Test format on empty value
    public function testFormatRemainingDurationOnEmptyValue(): void
    {
        $result = $this->extension->formatRemainingDuration(0);

        $this->assertEquals('', $result);
    }

    // Test on remaining days format
    public function testFormatRemainingDurationOnDays(): void
    {
        $result = $this->extension->formatRemainingDuration(500000);

        $this->assertEquals('6 days left', $result);
    }

    // Test on hours and minutes remaining format (double digits on minutes needed)
    public function testFormatRemainingDurationOnHourAndMinutes(): void
    {
        $result = $this->extension->formatRemainingDuration(4000);

        $this->assertEquals('1 h 06 min left', $result);
    }

    // Test only on remaining minutes format
    public function testFormatRemainingDurationOnMinutes(): void
    {
        $result = $this->extension->formatRemainingDuration(3000);

        $this->assertEquals('50 min left', $result);
    }
}