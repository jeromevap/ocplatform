<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\AdvertSkill;
use App\Entity\Application;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use function Sodium\add;

class LoadAdvert extends Fixture implements DependentFixtureInterface
{
    public function getDependencies() {
      return array(LoadSkill::class, LoadCategory::class, LoadImage::class);
    }

  public function load(ObjectManager $manager)
    {
        $advert = new Advert(
          'Recherche développeur Symfony.',
          'Alexandre',
          'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…'
        );

        $advert2 = new Advert(
          'Expert SQL pour projet d\'envergure',
          'Jojo la Frime',
          'Nous recherchons la perle rare qui sera capable de nous sortir d\'une merde noire.'
        );

        $advert3 = new Advert(
          'Mission de webmaster',
          'Hugo',
          'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…'
        );

        $advert4 = new Advert(
          'Offre de stage webdesigner',
          'Mathieu',
          'Nous proposons un poste pour webdesigner. Blabla…'
        );

      // On récupère toutes les compétences possibles
      $listSkills = $manager->getRepository('App:Skill')->findAll();

      // Pour chaque compétence
      foreach ($listSkills as $skill) {
        // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
        $advertSkill = new AdvertSkill();

        // On la lie à l'annonce, qui est ici toujours la même
        $advertSkill->setAdvert($advert);
        // On la lie à la compétence, qui change ici dans la boucle foreach
        $advertSkill->setSkill($skill);
        // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
        $advertSkill->setLevel('Expert');
        // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
        $manager->persist($advertSkill);
      }

      // Pour l'annonce 2, on ne récupère que la compétence SQL
        $aSkill = $manager->getRepository('App:Skill')->findOneBy(array('name' => 'SQL'));
      $sqlAdvertSkill = new AdvertSkill();
      $sqlAdvertSkill->setAdvert($advert2);
      $sqlAdvertSkill->setSkill($aSkill);
      $sqlAdvertSkill->setLevel('Expert');
      $manager->persist($sqlAdvertSkill);

      // Les catégories référencent les annonces. Le lien bidirectionnel est géré
      // dans les catégories.
      $categories = $manager->getRepository('App:Category')->findAll();

      foreach ($categories as $category) {
        $category->addAdvert($advert);
      }

      // On ajoute deux candidatures de test à la 1ère annonce.
      $app1 = new Application('John Doe', 'Je suis hyper-motivé !');
      $app2 = new Application('Jonny Halliday', 'Je ne sais pas ce que c\'est, mais ça me botte !');
      $app3 = new Application('France de la Patte Fueilletée', 'Pourquoi pas moi après tout ?');
      $advert->addApplication($app1);
      $advert->addApplication($app2);
      $advert2->addApplication($app3);

      $manager->persist($app1);
      $manager->persist($app2);
      $manager->persist($app3);

      // Doctrine ne connait pas encore l'entité $advert. Si vous n'avez pas défini la relation AdvertSkill
      // avec un cascade persist (ce qui est le cas si vous avez utilisé mon code), alors on doit persister $advert
      $manager->persist($advert);
      $manager->persist($advert2);
      $manager->persist($advert3);
      $manager->persist($advert4);

      $manager->flush();
    }
}
