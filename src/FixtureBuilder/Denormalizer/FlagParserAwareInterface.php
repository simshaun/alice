<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\Denormalizer;

interface FlagParserAwareInterface
{
    /**
     * @param FlagParserInterface $parser
     *
     * @return static
     */
    public function withFlagParser(FlagParserInterface $parser);
}
