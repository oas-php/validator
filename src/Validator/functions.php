<?php declare(strict_types=1);

namespace OAS\Validator;

use function iter\all;

function isNumber($value): bool
{
    return is_integer($value) || is_float($value);
}

/**
 * empty array is not considered a map
 *
 * @param $value
 * @return bool
 */
function isMap($value): bool
{
    return is_array($value) && !empty($value) && all('is_string', array_keys($value));
}

function isObject($value): bool
{
    return $value instanceof \stdClass || isMap($value);
}

/**
 * empty array is considered a list
 *
 * @param $value
 * @return bool
 */
function isList($value): bool
{
    return is_array($value) && all('is_integer', array_keys($value));
}

function isZeroDecimalFloat($value): bool
{
    return is_float($value) && $value == intval($value);
}

function zip(array $a, array $b): array
{
    return array_map(null, $a, $b);
}

function extract(array $keys, array $map): array
{
    return array_reduce(
        $keys,
        function ($subHashMap, $key) use ($map) {
            if (\array_key_exists($key, $map)) {
                $subHashMap[$key] = $map[$key];
            }

            return $subHashMap;
        },
        []
    );
}

function equal($valueA, $valueB): bool
{
    if (is_null($valueA)) {
        return is_null($valueB);
    }

    if (is_scalar($valueA)) {
        if (!is_scalar($valueB)) {
            return false;
        }

        if (isNumber($valueA)) {
            return equalNumbers($valueA, $valueB);
        }

        return $valueA === $valueB;
    }

    if ($valueA instanceof \stdClass) {
        return equalObjects($valueA, $valueB);
    }

    return equalLists($valueA, $valueB);
}

function normalize($value)
{
    if (is_array($value)) {
        $value = array_map(fn ($v) => normalize($v), $value);

        return isMap($value)
            ? (object) $value : $value;
    }

    return $value;
}

/**
 * according to spec: 1 == 1.0
 *
 * @param int|float $valueA
 * @param int|float $valueB
 * @return bool
 */
function equalNumbers($valueA, $valueB): bool
{
    return isNumber($valueA) && isNumber($valueB) && $valueA == $valueB;
}

function equalObjects($valueA, $valueB): bool
{
    if ($valueA instanceof \stdClass && $valueB instanceof \stdClass) {
        $valueA = (array) $valueA;
        $valueB = (array) $valueB;

        ksort($valueA);
        ksort($valueB);

        return array_keys($valueA) == array_keys($valueB) && equalArrays($valueA, $valueB);
    }

    return false;
}

function equalLists($valueA, $valueB): bool
{
    if (isList($valueA) && isList($valueB)) {
        if (count($valueA) != count($valueB)) {
            return false;
        }

        return equalArrays($valueA, $valueB);
    }

    return false;
}

function equalArrays(array $a, array $b): bool
{
    return all(
        fn ($values) => equal($values[0], $values[1]), zip($a, $b)
    );
}
