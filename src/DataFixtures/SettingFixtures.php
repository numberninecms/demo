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

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use NumberNine\Entity\CoreOption;
use NumberNine\Entity\Post;
use NumberNine\Model\General\Settings;

final class SettingFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $siteTitle = (new CoreOption())->setName(Settings::SITE_TITLE)
            ->setValue('NumberNine CMS | Every good business needs a good CMS software');
        $myAccount = (new CoreOption())->setName(Settings::PAGE_FOR_MY_ACCOUNT)
            ->setValue($this->getReference(Post::class . '_page_my_account')->getId()); // @phpstan-ignore-line
        $privacy = (new CoreOption())->setName(Settings::PAGE_FOR_PRIVACY)
            ->setValue($this->getReference(Post::class . '_page_privacy')->getId()); // @phpstan-ignore-line

        $manager->persist($siteTitle);
        $manager->persist($myAccount);
        $manager->persist($privacy);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PageFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['sample_data'];
    }
}
