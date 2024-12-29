<?php declare(strict_types=1);

namespace OAS\Resolver;

use Stringable;
use function Sabre\Uri\resolve;
use function Sabre\Uri\parse;
use function Sabre\Uri\build;

class Uri implements Stringable
{
    /**
     * @var array{fragment: ?string}
     */
    private array $parsed;

    public function __construct(private string $value)
    {
        $this->parsed = parse($this->value);
    }

    public function resolve(string $new): Uri
    {
        $new = new self($new);

        if ($new->isUrn()) {
            return $new;
        }

        if ($this->isUrn()) {
            //  TODO: exchange the last path segment!

            $parsed = $this->parsed;
            $parsed['fragment'] = $new->parsed['fragment'] ?? $this->parsed['fragment'];

            return new self(build($parsed));
        }

        return new Uri(resolve($this->value, $new->value));
    }

    public function withoutFragment(): Uri
    {
        $parsed = $this->parsed;
        unset($parsed['fragment']);

        return new Uri(build($parsed));
    }

    public function hasFragment(): bool
    {
        return is_string($this->parsed['fragment'] ?? null);
    }

    public function hasAnchor(): bool
    {
        return !str_starts_with($this->getFragment() ?? '', '/');
    }

    // TODO implement as \OAS\Resolver\realPath
    public function normalize(): string
    {
        return $this->value;
    }

    public function getFragment(): ?string
    {
        return $this->parsed['fragment'];
    }

    public function withFragment(string $fragment): Uri
    {
        return new Uri(build(array_merge($this->parsed, ['fragment' => $fragment])));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isUrn(): bool
    {
        //TODO change to regex
        return str_starts_with($this->value, 'urn:');
    }
}