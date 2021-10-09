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
use NumberNine\Entity\ContentEntityTerm;
use NumberNine\Entity\Post;
use NumberNine\Entity\Term;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ContentEntityTermFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        protected ContentService $contentService,
        private Generator $faker,
        private SluggerInterface $slugger,
    ) {
        parent::__construct($contentService);
    }

    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(
            ContentEntityTerm::class,
            FixtureSettings::POSTS_COUNT,
            function (ContentEntityTerm $contentEntityTerm, $i) {
                /** @var Term $category */
                $category = $this->getReference(
                    Term::class . '_' . (random_int(0, FixtureSettings::CATEGORIES_COUNT - 1))
                );

                /** @var Post $post */
                $post = $this->getReference(Post::class . '_post_' . $i);

                $contentEntityTerm
                    ->setContentEntity($post)
                    ->setTerm($category);

                $post->setTitle($this->faker->blogTitle($category->getName()));
                $post->setSlug($this->slugger->slug($post->getTitle()));
            }
        );

        $this->createMany(
            ContentEntityTerm::class,
            FixtureSettings::POSTS_COUNT * 2,
            function (ContentEntityTerm $contentEntityTerm, $i) {
                $contentEntityTerm
                    ->setContentEntity($this->getReference(
                        Post::class . '_post_' . (int)floor(($i - FixtureSettings::POSTS_COUNT) / 2)
                    ));

                if ($i % 2 === 0) {
                    $contentEntityTerm
                        ->setTerm($this->getReference(
                            Term::class . '_' . (random_int(
                                FixtureSettings::CATEGORIES_COUNT,
                                FixtureSettings::TAGS_COUNT / 2
                            ))
                        ));
                } else {
                    $contentEntityTerm
                        ->setTerm($this->getReference(
                            Term::class . '_' . (random_int(
                                FixtureSettings::CATEGORIES_COUNT + FixtureSettings::TAGS_COUNT / 2,
                                FixtureSettings::TAGS_COUNT
                            ))
                        ));
                }
            },
            FixtureSettings::POSTS_COUNT
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PostFixtures::class, TermFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
