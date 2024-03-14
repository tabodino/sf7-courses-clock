<?php

namespace App\Tests;

use App\Entity\Course;
use App\Repository\CourseRepository;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Clock\MonotonicClock;

class CourseControllerTest extends WebTestCase
{
    private const EUROPE_TIMEZONE = 'Europe/Paris';
    private const AMERICAN_TIMEZONE = 'America/New_York';
    private const INDIAN_TIMEZONE = 'Indian/Mauritius';

    /**
     * @return void
     */
    public function testCoursePageIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Clock Component example');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    public function testAvailableCourses(): void
    {
        $clock = new MockClock(self::EUROPE_TIMEZONE);
        $now = $clock->now();

        $unavailableCourse = $this->createMockCourse(self::EUROPE_TIMEZONE, 1, true);

        $testedCourses = array_filter([$unavailableCourse], function (Course $course) use ($now) {
            return $course->getAvailableAt() <= $now ?? [];
        });

        $this->assertCount(1, $testedCourses);
    }

    /**
     * @return void
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    public function testUnavailableCourses(): void
    {
        $clock = new MockClock(self::EUROPE_TIMEZONE);
        $now = $clock->now();

        $unavailableCourse = $this->createMockCourse(self::EUROPE_TIMEZONE, 1);

        $testedCourses = array_filter([$unavailableCourse], function (Course $course) use ($now) {
            return $course->getAvailableAt() <= $now ?? [];
        });

        $this->assertCount(0, $testedCourses);
    }

    /**
     * @return void
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    public function testCoursePageWithSameTimezone(): void
    {
        $delay = 3600;
        $client = static::createClient();

        $mockClock = new MockClock(self::EUROPE_TIMEZONE);
        $startTime = $mockClock->now()->getTimestamp();
        // Simulates a 1-hour delay
        $mockClock->sleep($delay);
        // Check 1-hour simulate time
        $this->assertEquals($delay, $mockClock->now()->getTimestamp() - $startTime);

        $course = $this->createMockCourse(self::EUROPE_TIMEZONE, 1, true);
        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())->method('findAll')->willReturn([$course]);

        $now = $mockClock->now();

        $client->getContainer()->set(CourseRepository::class, $courseRepository);

        $testedCourses = array_filter([$course], function (Course $course) use ($now) {
            return $course->getAvailableAt() <= $now ?? [];
        });

        $this->assertCount(1, $testedCourses);

        $client->request('GET', '/');

        $this->assertSelectorTextContains('h1', 'Clock Component example');
        $this->assertSelectorTextContains('h2', 'Available courses');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @return void
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    public function testCoursePageWithPastTimezone(): void
    {
        $client = static::createClient();

        $course = $this->createMockCourse(self::EUROPE_TIMEZONE, 1);
        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())->method('findAll')->willReturn([$course]);

        $clockNewYork = new MockClock(self::AMERICAN_TIMEZONE);

        $client->getContainer()->set(CourseRepository::class, $courseRepository);

        $client->request('GET', '/');

        $testedCourses = array_filter([$course], function (Course $course) use ($clockNewYork) {
            return $course->getAvailableAt() <= $clockNewYork ?? [];
        });

        $this->assertCount(0, $testedCourses);
    }

    /**
     * @return void
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    public function testCoursePageWithFutureTimezone()
    {
        $client = static::createClient();

        $course = $this->createMockCourse(self::EUROPE_TIMEZONE, 1, true);
        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())->method('findAll')->willReturn([$course]);

        $mockClock = new MockClock(self::INDIAN_TIMEZONE);

        $client->getContainer()->set(CourseRepository::class, $courseRepository);

        $client->request('GET', '/');

        $testedCourses = array_filter([$course], function (Course $course) use ($mockClock) {
            return $course->getAvailableAt() <= $mockClock->now() ?? [];
        });

        $this->assertCount(1, $testedCourses);
    }

    /**
     * @param \DateTimeZone|string $timeZone
     * @param int $nbDay
     * @param boolean $isNegative
     * @return Course
     * @throws \DateInvalidTimeZoneException
     * @throws \DateMalformedStringException
     */
    private function createMockCourse(\DateTimeZone|string $timeZone, int $nbDay, $isNegative = false): Course
    {
        $nbDay = abs($nbDay);
        $modifyDay = ($isNegative) ? "- $nbDay day" : "+ $nbDay day";

        $clock = new MockClock();
        try {
            $clock->withTimeZone($timeZone);
        } catch (\DateInvalidTimeZoneException $e) {
            throw new \DateInvalidTimeZoneException($e->getMessage());
        }
        $mockDate = $clock->now()->modify($modifyDay);

        return (new Course())->setTitle('test')->setThumbnail('test')->setAvailableAt($mockDate);
    }
}
