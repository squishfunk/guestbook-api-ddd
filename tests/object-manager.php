<?php

use App\Kernel;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

/** @var Registry $doctrine */
$doctrine = $kernel->getContainer()->get('doctrine');

/** @var EntityManagerInterface $entityManager */
$entityManager = $doctrine->getManager();

return $entityManager;
