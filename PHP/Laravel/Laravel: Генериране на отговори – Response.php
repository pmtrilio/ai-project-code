<?php

namespace Illuminate\Http;

use ArrayObject;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Response extends SymfonyResponse
{
    use ResponseTrait, Macroable {
        Macroable::__call as macroCall;
    }

    /**
     * Create a new HTTP response.
     *
     * @param  mixed  $content
     * @param  int  $status
     * @param  array  $headers
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($content = '', $status = 200, array $headers = [])
    {
        $this->headers = new ResponseHeaderBag($headers);

        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
    }

    /**
     * Get the response content.
     */
    #[\Override]
    public function getContent(): string|false
    {
        return transform(parent::getContent(), fn ($content) => $content, '');
    }

    /**
     * Set the content on the response.
     *
     * @param  mixed  $content
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    #[\Override]
    public function setContent(mixed $content): static
    {
        $this->original = $content;
