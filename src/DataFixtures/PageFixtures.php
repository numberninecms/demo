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

use DateTime;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use NumberNine\Content\ContentService;
use NumberNine\DataFixtures\BaseFixture;
use NumberNine\Entity\Post;
use NumberNine\Entity\User;
use NumberNine\Model\Content\PublishingStatusInterface;
use NumberNine\Repository\PostRepository;
use Symfony\Component\HttpKernel\KernelInterface;

final class PageFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public const PAGES = ['Home', 'About', 'Contact', 'Our stores', 'Blog', 'Faq'];

    public function __construct(
        protected ContentService $contentService,
        private PostRepository $postRepository,
        private Generator $faker,
        private KernelInterface $kernel,
    ) {
        parent::__construct($contentService);
    }

    public function loadData(ObjectManager $manager): void
    {
        /** @var User $admin */
        $admin = $this->getReference(User::class . '_administrator');

        foreach (self::PAGES as $pageName) {
            $page = (new Post())
                ->setCustomType('page')
                ->setTitle($pageName)
                ->setContent($this->faker->text(2000))
                ->setAuthor($admin)
                ->setStatus(PublishingStatusInterface::STATUS_PUBLISH)
                ->setCreatedAt(new DateTime())
                ->setPublishedAt(new DateTime());

            $this->setReference(Post::class . '_page_' . strtolower(str_replace(' ', '_', $pageName)), $page);
            $manager->persist($page);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
