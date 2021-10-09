<?php

/*
 * This file is part of the NumberNine package.
 *
 * (c) William Arin <williamarin.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use NumberNine\Content\ContentService;
use NumberNine\DataFixtures\BaseFixture;
use NumberNine\DataFixtures\TaxonomyFixtures;
use NumberNine\Entity\Taxonomy;
use NumberNine\Entity\Term;

final class TermFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        protected ContentService $contentService,
        private Generator $faker,
    ) {
        parent::__construct($contentService);
    }

    public function loadData(ObjectManager $manager): void
    {
        $categories = ['Art', 'Food', 'Lifestyle', 'Movie', 'Music', 'Travel'];

        /** @var Taxonomy $taxonomyCategory */
        $taxonomyCategory = $this->getReference('taxonomy_category');

        /** @var Taxonomy $taxonomyTag */
        $taxonomyTag = $this->getReference('taxonomy_tag');

        $this->createMany(
            Term::class,
            count($categories),
            static function (Term $term, $i) use ($categories, $taxonomyCategory) {
                $term
                    ->setTaxonomy($taxonomyCategory)
                    ->setName($categories[$i]);
            }
        );

        $this->createMany(
            Term::class,
            FixtureSettings::TAGS_COUNT,
            function (Term $term) use ($taxonomyTag) {
                $term
                    ->setTaxonomy($taxonomyTag)
                    ->setName($this->faker->safeColorName);
            },
            count($categories)
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TaxonomyFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
