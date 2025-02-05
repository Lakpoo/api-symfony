<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EditorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $editors = [
            ['name' => 'Valve', 'country' => 'USA'],
            ['name' => 'Riot Games', 'country' => 'USA'],
            ['name' => 'Mojang Studios', 'country' => 'Sweden'],
        ];

        foreach ($editors as $editorData) {
            $editor = new Editor();
            $editor->setName($editorData['name'])
                ->setCountry($editorData['country']);

            $manager->persist($editor);
        }

        $manager->flush();
    }
}