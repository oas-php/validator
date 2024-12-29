<?php declare(strict_types=1);

namespace OAS\Resolver;

function pathSegments(string $path, string $segmentSeparator = DIRECTORY_SEPARATOR): array
{
    if (str_starts_with($path, '#')) {
        $path = substr($path, 1);
    }

    if (str_starts_with($path, $segmentSeparator)) {
        $path = substr($path, 1);
    }

    if ($path == '') {
        return [];
    }

    return explode($segmentSeparator, $path);
}

function join(string $pathA, string $pathB, $pathSeparator = DIRECTORY_SEPARATOR): string
{
    if ($pathA == "") {
        return $pathSeparator . $pathB;
    }

    if ($pathB == "") {
        return $pathA . $pathSeparator;
    }

    $pathASegments = pathSegments($pathA, $pathSeparator);
    $pathBSegments = pathSegments($pathB, $pathSeparator);

    $result =  $pathSeparator . \join($pathSeparator,
        array_merge(
            $pathASegments,
            $pathBSegments
        )
    );

    return $result;
}

function encode(string $value): string
{
    return urlencode(
        jsonPointerEncode($value)
    );
}

function decode(string $encoded): string
{
    return jsonPointerDecode(
        urldecode($encoded)
    );
}

function jsonPointerEncode(string $jsonPointer): string
{
    return str_replace(['~', '/'], ['~0', '~1'], $jsonPointer);
}

function jsonPointerDecode(string $encodedJsonPointer): string
{
    return str_replace(['~0', '~1'], ['~', '/'], $encodedJsonPointer);
}
