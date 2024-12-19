<?php

namespace App\Command;

use Dotenv\Dotenv;
use Faker\Factory;
use Pobo\Exceptions\ApiClientException;
use Pobo\Exceptions\AuthenticationException;
use Pobo\UserClient;
use Pobo\PoboClient;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportProductsCommand extends Command
{
    protected static string $defaultName = 'app:products:export';
    public PoboClient $poboClient;

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
                $_ENV['POBO_PASSWORD'],
            );
            $this->poboClient = new PoboClient($user);
        } catch (AuthenticationException $e) {
            throw new RuntimeException(sprintf('Pobo authentication failed: %s', $e->getMessage()));
        }

        try {
            $faker = Factory::create('cs_CZ');
            $products = [];

            for ($i = 0; $i < 50; $i++) {
                $products[] = [
                    'guid' => $faker->uuid(),
                    'name' => $faker->name() . ' ' . $faker->word,
                    'short_description' => $faker->sentence(3),
                    'url' => $faker->url(),
                    'description' => sprintf(
                        '<h2>%s</h2><p>%s</p><ul><li>%s</li><li>%s</li><li>%s</li></ul><p>%s</p>',
                        $faker->catchPhrase,
                        $faker->paragraph(2),
                        $faker->sentence,
                        $faker->sentence,
                        $faker->sentence,
                        $faker->paragraph
                    ),
                    'is_visible' => $faker->boolean(80),
                    'categories' => array_rand(range(1, 10), 3),
                    'images' => [
                        [
                            'src' => sprintf('https://picsum.photos/%d/%d', $faker->numberBetween(200, 800), $faker->numberBetween(200, 800)),
                        ],
                        [
                            'src' => sprintf('https://picsum.photos/%d/%d', $faker->numberBetween(200, 800), $faker->numberBetween(200, 800)),
                            'main_image' => true,
                        ],
                    ]
                ];
            }

            $client = new PoboClient($user);
            $result = $client->products()->bulkImport($products);

            $table = new Table($output);
            $table
                ->setHeaders(['Success', 'Skipped', 'Errors'])
                ->setRows([
                    [
                        $result['result']['success'] ?? 0,
                        $result['result']['skipped'] ?? 0,
                        implode(', ', $result['result']['errors'] ?? []),
                    ]
                ]);

            $table->render();

        } catch (ApiClientException $e) {
            throw new RuntimeException(sprintf('Pobo API client error: %s', $e->getMessage()));
        }

        return Command::SUCCESS;
    }
}