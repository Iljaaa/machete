<?php

namespace Iljaaa\Machete;

use Iljaaa\Machete\rules\RulesCollection;

class ValidationResult
{

    /**
     * @var bool
     */
    private bool $isValid = false;

    /**
     * @var RulesCollection|null
     */
    private ?RulesCollection $rules = null;

    /**
     * Set validation result in true
     * and clear saved rules
     * @return void
     */
    public function clearBeforeValidate(): ValidationResult
    {
        $this->isValid = true;
        $this->rules = null;
        return $this;
    }

    /**
     * Write is valid state flag
     * @param bool $isValid
     * @return $this
     */
    public function setIsValid(bool $isValid): ValidationResult
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * @param RulesCollection $rules
     * @return $this
     */
    public function setRulesCollection(RulesCollection $rules): ValidationResult
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid (): bool
    {
        return $this->isValid;
    }

    /**
     * @return array
     */
    public function getErrors (): array
    {
        return $this->rules->getErrors();
    }

    /**
     * @return string
     */
    public function getFirstError (): string
    {
        return $this->rules->getFirstError();
    }

    /**
     * @param string $attribute
     * @return array
     */
    public function getErrorsForAttribute (string $attribute): array
    {
        return $this->rules->getErrorsForAttribute($attribute);
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFirstErrorForAttribute (string $attribute): string
    {
        return $this->rules->getFirstErrorForAttribute($attribute);
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function isAttributeValid (string $attribute): bool
    {
        return $this->rules->isAttributeValid ($attribute);
    }

}
