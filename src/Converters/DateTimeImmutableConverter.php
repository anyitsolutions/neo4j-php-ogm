<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Converters;

use GraphAware\Neo4j\OGM\Exception\ConverterException;

class DateTimeImmutableConverter extends Converter
{
    const DEFAULT_FORMAT = 'timestamp';

    const LONG_TIMESTAMP_FORMAT = 'long_timestamp';

    public function getName()
    {
        return 'datetime_immutable';
    }

    public function toDatabaseValue($value, array $options)
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {

            $format = isset($options['format']) ? $options['format'] : self::DEFAULT_FORMAT;

            if (self::DEFAULT_FORMAT === $format) {
                return $value->getTimestamp();
            }

            if (self::LONG_TIMESTAMP_FORMAT === $format) {
                return $value->getTimestamp() * 1000;
            }

            try {
                return $value->format($format);
            } catch (\Exception $e) {
                throw new ConverterException(sprintf('Error while converting timestamp: %s', $e->getMessage()));
            }

        }

        throw new ConverterException(sprintf('Unable to convert value in converter "%s"', $this->getName()));
    }

    public function toPHPValue(array $values, array $options)
    {
        if (!isset($values[$this->propertyName]) || null === $values[$this->propertyName]) {
            return null;
        }

        $tz = isset($options['timezone']) ? new \DateTimeZone($options['timezone']) : new \DateTimeZone(date_default_timezone_get());

        $format = isset($options['format']) ? $options['format'] : self::DEFAULT_FORMAT;
        $v = $values[$this->propertyName];

        if (self::DEFAULT_FORMAT === $format) {
            return \DateTimeImmutable::createFromFormat('U', $v, $tz);
        }

        if (self::LONG_TIMESTAMP_FORMAT === $format) {
            return \DateTimeImmutable::createFromFormat('U', round($v/1000), $tz);
        }

        return \DateTimeImmutable::createFromFormat($format, $v);

    }

}
