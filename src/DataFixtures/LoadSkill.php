<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Skill;

class LoadSkill extends Fixture
{

    public function load(ObjectManager $manager)
    {
        // Liste des noms de compétences à ajouter
        $names = [
          'PHP',
          'Symfony',
          'C++',
          'Java',
          'Photoshop',
          'Blender',
          'Bloc-note',
          'SQL',
        ];

        foreach ($names as $name) {
            // On crée la compétence
            $skill = new Skill();
            $skill->setName($name);

            // On la persiste
            $manager->persist($skill);
        }

        $manager->flush();
    }

}
