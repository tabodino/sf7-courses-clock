<?php

namespace App\DataFixtures;

use App\Entity\Course;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\ClockInterface;

class CourseFixtures extends Fixture
{
    private const NB_AVAILABLE_COURSES = 3;
    private const NB_UNAVAILABLE_COURSES = 6;

    public function __construct(private readonly ClockInterface $clock)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $title = ['Native', 'Mock', 'Monotonic'];
        $content = '
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam aliquet venenatis massa, quis posuere eros
            eleifend a. Nam feugiat sapien sed erat dapibus, eget faucibus ex volutpat. Praesent non varius leo. Etiam
            nec dapibus felis. Curabitur est ex, tincidunt in tristique in, porta sit amet tortor.';
        $now = $this->clock->now();

        for ($i = 0; $i < self::NB_AVAILABLE_COURSES; $i++) {
            $course = (new Course())
                ->setTitle("Discover {$title[$i]} clock")
                ->setThumbnail("https://picsum.photos/id/{$i}/300/300")
                ->setContent($content)
                ->setAvailableAt($now)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            $manager->persist($course);
        }

        for ($i = 1; $i <= self::NB_UNAVAILABLE_COURSES; $i++) {
            // Add an extra month for each item
            $newAvailableAt = $now->add(new DateInterval('P' . $i . 'M'));
            $course = (new Course())
                ->setTitle("Discover a new topic #{$i}")
                ->setThumbnail("https://picsum.photos/id/0/300/300")
                ->setContent($content)
                ->setAvailableAt($newAvailableAt)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            $manager->persist($course);
        }
        $manager->flush();
    }
}
