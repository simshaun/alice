<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\Chainable;

use Nelmio\Alice\Definition\FlagBag;
use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\NotCallableTrait;

class FakeAbstractChainableDenormalizer extends AbstractChainableDenormalizer
{
    use NotCallableTrait;

    /**
     * @inheritdoc
     */
    public function canDenormalize(string $reference): bool
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function denormalize(
        FixtureBag $builtFixtures,
        string $className,
        string $fixtureId,
        array $specs,
        FlagBag $flags
    ): FixtureBag
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function denormalizeTemporaryFixture(
        FixtureBag $builtFixtures,
        string $className,
        array $specs,
        FlagBag $flags
    ): array
    {
        return parent::denormalizeTemporaryFixture($builtFixtures, $className, $specs, $flags);
    }
}
