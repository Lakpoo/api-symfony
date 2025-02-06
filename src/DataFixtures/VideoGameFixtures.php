<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use App\Entity\VideoGame;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            EditorFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $videoGames = [
            ['title' => 'Counter-Strike: Global Offensive', 'releaseDate' => '2012-08-21', 'description' => 'A multiplayer first-person shooter developed by Valve and Hidden Path Entertainment.'],
            ['title' => 'Counter-Strike 2', 'releaseDate' => '2023-09-01', 'description' => 'The latest installment in the Counter-Strike series.'],
            ['title' => 'Valorant', 'releaseDate' => '2020-06-02', 'description' => 'A free-to-play first-person tactical hero shooter developed and published by Riot Games.'],
            ['title' => 'Minecraft', 'releaseDate' => '2011-11-18', 'description' => 'A sandbox video game developed by Mojang Studios.'],
        ];

        foreach ($videoGames as $gameData) {
            $videoGame = new VideoGame();
            $videoGame->setTitle($gameData['title'])
                ->setReleaseDate(new DateTime($gameData['releaseDate']))
                ->setDescription($gameData['description']);
            $editorRef = EditorFixtures::EDITOR_ID.rand(0, 2);
            $videoGame->setEditor($this->getReference($editorRef, Editor::class));
            $manager->persist($videoGame);
        }

        $manager->flush();
    }
}