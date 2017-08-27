<?php namespace Limoncello\Flute\Validation;

/**
 * Copyright 2015-2017 info@neomerx.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Limoncello\Flute\Contracts\Validation\JsonApiValidatorInterface;
use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;

/**
 * @package Limoncello\Flute
 */
abstract class ValidatorWrapper implements JsonApiValidatorInterface
{
    /**
     * @var JsonApiValidatorInterface
     */
    private $validator;

    /**
     * @var int
     */
    private $httpErrorCode;

    /**
     * @var array
     */
    private $wrapperCaptures = [];

    /**
     * @var array
     */
    private $wrapperErrors = [];

    /**
     * @var bool
     */
    private $overrideNotReplace = true;

    /**
     * @param JsonApiValidatorInterface $validator
     * @param int                       $httpErrorCode
     */
    public function __construct(
        JsonApiValidatorInterface $validator,
        int $httpErrorCode = JsonApiException::DEFAULT_HTTP_CODE
    ) {
        $this->validator     = $validator;
        $this->httpErrorCode = $httpErrorCode;
    }

    /**
     * @inheritdoc
     */
    public function assert($jsonData): JsonApiValidatorInterface
    {
        if ($this->validate($jsonData) === false) {
            throw new JsonApiException($this->getJsonApiErrors(), $this->getHttpErrorCode());
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getJsonApiCaptures(): array
    {
        $captures = $this->getWrapperCaptures();

        return $this->isOverrideCapturesNotReplace() === true ?
            array_merge($this->getWrappedValidator()->getJsonApiCaptures(), $captures) : $captures;
    }

    /**
     * @inheritdoc
     */
    public function getJsonApiErrors(): array
    {
        return array_merge($this->getWrappedValidator()->getJsonApiErrors(), $this->getWrapperErrors());
    }

    /**
     * @param array $wrapperCaptures
     *
     * @return self
     */
    protected function setWrapperCaptures(array $wrapperCaptures): self
    {
        $this->wrapperCaptures    = $wrapperCaptures;
        $this->overrideNotReplace = true;

        return $this;
    }

    /**
     * @param array $wrapperCaptures
     *
     * @return self
     */
    protected function setCaptureReplacements(array $wrapperCaptures): self
    {
        $this->wrapperCaptures    = $wrapperCaptures;
        $this->overrideNotReplace = false;

        return $this;
    }

    /**
     * @return array
     */
    protected function getWrapperCaptures(): array
    {
        return $this->wrapperCaptures;
    }

    /**
     * @return array
     */
    protected function getWrapperErrors(): array
    {
        return $this->wrapperErrors;
    }

    /**
     * @param ErrorInterface[] $wrapperErrors
     *
     * @return self
     */
    protected function setWrapperErrors(array $wrapperErrors): self
    {
        assert(call_user_func(function () use ($wrapperErrors) : bool {
            $allAreErrors = true;

            foreach ($wrapperErrors as $error) {
                $allAreErrors = $allAreErrors === true && $error instanceof ErrorInterface;
            }

            return $allAreErrors;
        }), 'All errors should implement ErrorInterface.');

        $this->wrapperErrors = $wrapperErrors;

        return $this;
    }

    /**
     * @return JsonApiValidatorInterface
     */
    protected function getWrappedValidator(): JsonApiValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return int
     */
    private function getHttpErrorCode(): int
    {
        return $this->httpErrorCode;
    }

    /**
     * @return bool
     */
    private function isOverrideCapturesNotReplace(): bool
    {
        return $this->overrideNotReplace;
    }
}