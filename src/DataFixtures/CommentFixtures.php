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
use NumberNine\Entity\Comment;
use NumberNine\Entity\Post;
use NumberNine\Entity\User;
use NumberNine\Model\Content\CommentStatusInterface;

final class CommentFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        protected ContentService $contentService,
        private Generator $faker,
    ) {
        parent::__construct($contentService);
    }

    public function loadData(ObjectManager $manager): void
    {
        $status = [
            CommentStatusInterface::COMMENT_STATUS_PENDING,
            CommentStatusInterface::COMMENT_STATUS_APPROVED,
        ];

        $this->createMany(
            Comment::class,
            FixtureSettings::POSTS_COUNT * 3,
            function (Comment $comment, $i) use ($status) {
                $comment
                    ->setStatus($status[random_int(0, 1)])
                    ->setContent($this->faker->text)
                    ->setContentEntity($this->getReference(Post::class . '_post_' . (int)floor($i / 3)))
                    ->setAuthor($this->getRandomReference(User::class));

                if ($i % 3 === 2 && random_int(0, 2) === 0) {
                    $comment->setParent($this->getReference(Comment::class . '_' . ($i - 1)));
                }
            }
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, PostFixtures::class, PageFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
