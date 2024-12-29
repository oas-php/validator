<?php declare(strict_types=1);

namespace OAS\Resolver\Node;

use OAS\Resolver\DynamicReference;
use OAS\Resolver\Node;
use OAS\Resolver\Reference;
use OAS\Resolver\Ref;
use OAS\Resolver\Uri;
use stdClass;
use function OAS\Resolver\encode;
use function OAS\Resolver\join;
use function OAS\Resolver\pathSegments;

class Factory
{
    public function create(
        mixed $data,
        Uri $uri,
        ?Uri $canonicalUriBase = null,
        ?Node $parent = null
    ): Node
    {
        $meta = [];
        $canonicalUri = null;

        if ($data instanceof stdClass) {
            $data = (array) $data;
            $meta['emptyObject'] = empty($data);
        }

        if (is_array($data)) {
            // TODO: check $id context (just like with $ref / $dynamicRef)
            if (array_key_exists('$id', $data) && is_string($data['$id'])) {
                $id = $data['$id'];

                $canonicalUri = ($canonicalUriBase ?? $uri)->resolve($id);
            }

            $node = new Node($uri, $canonicalUri, children: [], meta: $meta, parent: $parent);

            foreach ($data as $path => $childNode) {
                $path = (string) $path;

                $node->addChild(
                    $path,
                    match (true) {
                        $this->isRef($path, $childNode, $uri) => new Reference(
                            new Ref(
                                $childNode,
                                ($canonicalUri ?? $canonicalUriBase ?? $uri)->resolve($childNode)
                            ),
                            $node
                        ),
                        $this->isDynamicRef($path, $childNode, $uri) => new DynamicReference(
                            new Ref(
                                $childNode,
                                ($canonicalUri ?? $canonicalUriBase ?? $uri)->resolve($childNode)
                            ),
                            $node
                        ),
                        default => $this->create(
                            $childNode,
                            $uri->withFragment(
                                // TODO Uri::appendFragment()
                                join(
                                    $uri->getFragment() ?? '',
                                    encode($path)
                                )
                            ),
                            $canonicalUri ?? $canonicalUriBase,
                            $node
                        )
                    }
                );
            }
        } else {
            $node = new Node($uri, $canonicalUri, value: $data, meta: $meta, parent: $parent);
        }

        return $node;
    }

    private function isRef(string $path, mixed $childNode, Uri $uri): bool
    {
        return '$ref' == $path && is_string($childNode) && $this->isSchemaContext($uri->getFragment() ?? '');
    }

    private function isDynamicRef(string $path, mixed $childNode, Uri $uri): bool
    {
        return '$dynamicRef' == $path && is_string($childNode) && $this->isSchemaContext($uri->getFragment() ?? '');
    }

    // TODO: make it more reliable :)
    private function isSchemaContext(string $path): bool
    {
        $pathSegments = pathSegments($path);

        if (!empty($pathSegments)) {
            // "properties" is immediate parent
            if ('properties' == $pathSegments[array_key_last($pathSegments)]) {
                return false;
            }

            // inside "enum"
            foreach ($pathSegments as $segment) {
                if ('enum' == $segment) {
                    return false;
                }
            }
        }

        return true;
    }
}
