<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CourseController extends AbstractController
{
    #[Route('/', name: 'courses.index')]
    public function index(CourseRepository $courseRepository): Response
    {
        $clock = new NativeClock();
        $now = $clock->now();
        $courses = $courseRepository->findAll();

        $availableCourses = array_filter($courses, function (Course $course) use ($now) {
            return $course->getAvailableAt() <= $now ?? [];
        });

        return $this->render('course/index.html.twig', [
            'availableCourses' => $availableCourses,
            'unavailableCourses' => array_diff_assoc($courses, $availableCourses)
        ]);
    }
}
