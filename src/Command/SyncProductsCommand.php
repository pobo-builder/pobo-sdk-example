<?php

namespace App\Command;

use Dotenv\Dotenv;
use Pobo\Exceptions\ApiClientException;
use Pobo\Exceptions\AuthenticationException;
use Pobo\UserClient;
use Pobo\PoboClient;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncProductsCommand extends Command
{
    protected static string $defaultName = 'app:sync-products';

    /** @var PoboClient  */
    private PoboClient $poboClient;

    public function __construct()
    {
        parent::__construct(self::$defaultName);

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $user = new UserClient(
                $_ENV['POBO_EMAIL'],
                $_ENV['POBO_PASSWORD']
            );
            $this->poboClient = new PoboClient($user);
        } catch (AuthenticationException $e) {
            throw new RuntimeException('Pobo authentication failed: ' . $e->getMessage());
        }


        try {
            $client = new PoboClient($user);
            $result = $client->products()->bulkImport(
                [
                    [
                        'guid' => '302b8ad6-07d5-11ec-b98c-0cc47a6c9371',
                        'name' => 'Test Product from API',
                        'short_description' => 'This is a test product created via API.',
                        'description' => '<p>HTML description of the product.</p>',
                        'is_visible' => true,
                        'categories' => [1, 2, 3],
                        'images' => [
                            [
                                'src' => 'https://picsum.photos/200/300',
                            ],
                            [
                                'src' => 'https://picsum.photos/200/300',
                                'main_image' => true,
                            ],
                        ]
                    ],
                ]
            );


            print_r($result);

            /*
            $table = new Table($output);
            $table
                ->setHeaders(['Metric', 'Value'])
                ->setRows([
                    ['Success', $result['result']['success']],
                    ['Skipped', $result['result']['skipped']],
                    ['Errors', empty($result['result']['errors']) ? 'None' : implode(', ', $result['result']['errors'])]
                ]);
            $table->render();
            */
        } catch (ApiClientException $e) {
            throw new RuntimeException('Pobo API client error: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
