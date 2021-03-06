<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\FixtureBuilder\ExpressionLanguage\Parser\TokenParser\Chainable;

use Nelmio\Alice\Definition\Value\OptionalValue;
use Nelmio\Alice\Exception\FixtureBuilder\ExpressionLanguage\ParseException;
use Nelmio\Alice\FixtureBuilder\ExpressionLanguage\Token;
use Nelmio\Alice\FixtureBuilder\ExpressionLanguage\TokenType;

final class OptionalTokenParser extends AbstractChainableParserAwareParser
{
    //TODO: review those kinds of constants and consider making them internals
    /** @internal */
    const REGEX = '/^(?<quantifier>\d+|\d*\.\d+|<.+>)%\? \ *?(?<first_member>[^:]+)(?:\: +(?<second_member>[^\ ]+))?/';

    /**
     * @inheritdoc
     */
    public function canParse(Token $token): bool
    {
        return $token->getType()->getValue() === TokenType::OPTIONAL_TYPE;
    }

    /**
     * Parses expressions such as '60%? foo: bar'.
     *
     * {@inheritdoc}
     *
     * @throws ParseException
     */
    public function parse(Token $token): OptionalValue
    {
        parent::parse($token);

        if (1 !== preg_match(self::REGEX, $token->getValue(), $matches)) {
            throw ParseException::createForToken($token);
        }

        return new OptionalValue(
            $this->parser->parse($matches['quantifier']),
            $this->parser->parse(trim($matches['first_member'])),
            array_key_exists('second_member', $matches)
                ? $this->parser->parse($matches['second_member'])
                : null
        );
    }
}
