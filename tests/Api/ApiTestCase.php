<?php
declare(strict_types=1);

namespace App\Tests\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ApiTestCase extends WebTestCase
{
    protected readonly UserRepository $userRepository;

    public function __construct(
    ) {
        parent::__construct();
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel(['environment' => 'test']);
        $application = new Application($kernel);

        $command = $application->find('doctrine:fixtures:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }
}
