<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture;

use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\FixtureBuilder\Denormalizer\FixtureBagDenormalizerInterface;
use Nelmio\Alice\FixtureBuilder\Denormalizer\FlagParserInterface;
use Nelmio\Alice\NotClonableTrait;

final class SimpleFixtureBagDenormalizer implements FixtureBagDenormalizerInterface
{
    use NotClonableTrait;
    
    /**
     * @var FixtureDenormalizerInterface
     */
    private $fixtureDenormalizer;

    /**
     * @var FlagParserInterface
     */
    private $flagParser;

    public function __construct(FixtureDenormalizerInterface $fixtureDenormalizer, FlagParserInterface $flagParser)
    {
        $this->fixtureDenormalizer = $fixtureDenormalizer;
        $this->flagParser = $flagParser;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $data subset of PHP data coming from the parser (does not contains any parameters)
     *
     * @example
     *  $data = [
     *      'Nelmio\Alice\Entity\User' => [
     *          'user0' => [
     *              'username' => 'bob',
     *          ],
     *      ],
     *  ];
     *
     * @return FixtureBag
     */
    public function denormalize(array $data): FixtureBag
    {
        $fixtures = new FixtureBag();
        foreach ($data as $fqcnWithFlags => $rawFixtureSet) {
            $flags = $this->flagParser->parse($fqcnWithFlags);
            $fqcn = $flags->getKey();

            foreach ($rawFixtureSet as $reference => $specs) {
                $fixtures = $this->fixtureDenormalizer->denormalize(
                    $fixtures,
                    $fqcn,
                    $reference,
                    $specs,
                    $flags
                );
            }
        }
        
        return $fixtures;
    }
}
