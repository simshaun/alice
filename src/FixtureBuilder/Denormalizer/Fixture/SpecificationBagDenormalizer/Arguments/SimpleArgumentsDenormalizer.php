<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\SpecificationBagDenormalizer\Arguments;

use Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\SpecificationBagDenormalizer\ArgumentsDenormalizerInterface;
use Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\SpecificationBagDenormalizer\ValueDenormalizerInterface;
use Nelmio\Alice\FixtureBuilder\Denormalizer\FlagParserInterface;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\NotClonableTrait;

final class SimpleArgumentsDenormalizer implements ArgumentsDenormalizerInterface
{
    use NotClonableTrait;

    /**
     * @var ValueDenormalizerInterface
     */
    private $valueDenormalizer;

    public function __construct(ValueDenormalizerInterface $valueDenormalizer)
    {
        $this->valueDenormalizer = $valueDenormalizer;
    }

    /**
     * @inheritdoc
     */
    public function denormalize(
        FixtureInterface $scope,
        FlagParserInterface $parser,
        array $unparsedArguments
    ): array
    {
        $arguments = [];
        foreach ($unparsedArguments as $unparsedIndex => $argument) {
            $argumentFlags = (is_string($unparsedIndex)) ? $parser->parse($unparsedIndex) : null;
            $arguments[] = $this->valueDenormalizer->denormalize($scope, $argumentFlags, $argument);
        }

        return $arguments;
    }
}
