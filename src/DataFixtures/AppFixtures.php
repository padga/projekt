<?php
/**
 * App fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * App fixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * load.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}