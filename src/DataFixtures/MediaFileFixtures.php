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
use NumberNine\DataFixtures\BaseFixture;
use NumberNine\Entity\ContentEntityRelationship;
use NumberNine\Entity\MediaFile;
use NumberNine\Entity\Post;
use NumberNine\Model\Content\PublishingStatusInterface;
use NumberNine\Content\ContentService;
use NumberNine\Media\MediaFileFactory;

final class MediaFileFixtures extends BaseFixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function __construct(
        protected ContentService $contentService,
        private Generator $faker,
        private MediaFileFactory $mediaFileFactory,
    ) {
        parent::__construct($contentService);
    }

    public function loadData(ObjectManager $manager): void
    {
        // Post featured images
        $this->createManyContentEntity(
            'media_file',
            FixtureSettings::POSTS_COUNT,
            function (MediaFile &$mediaFile, int $i) use ($manager) {
                /** @var Post $post */
                $post = $this->getReference(Post::class . '_post_' . $i);

                $mediaFile = $this->mediaFileFactory->createMediaFileFromFilename(
                    // @phpstan-ignore-next-line
                    $this->faker->blogFeaturedImage($post->getTerms('category')[0]->getName()),
                    // @phpstan-ignore-next-line
                    $post->getAuthor(),
                    false,
                    false,
                    false
                );

                $mediaFile
                    ->setTitle($post->getTitle() . ' Featured Image')
                    ->setStatus(PublishingStatusInterface::STATUS_PUBLISH);

                $relationship = (new ContentEntityRelationship())
                    ->setName('featured_image')
                    ->setParent($post)
                    ->setChild($mediaFile);

                $manager->persist($relationship);

                $post->addChild($relationship);
            }
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PostFixtures::class, ContentEntityTermFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
