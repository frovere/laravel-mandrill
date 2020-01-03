<?php

namespace Felrov\Drill;

class MandrillTemplate
{
    protected $id;
    protected $templateContent = [];
    protected $globalMergeVars = [];
    protected $mergeVars = [];
    protected $mergeLanguage = 'mailchimp';

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function setGlobalMergeVars(array $globalMergeVars): self
    {
        $this->globalMergeVars = $globalMergeVars;

        return $this;
    }

    public function setMergeVars(array $mergeVars): self
    {
        $this->mergeVars = $mergeVars;

        return $this;
    }

    public function setMergeLanguage(string $mergeLanguage): self
    {
        $this->mergeLanguage = $mergeLanguage;

        return $this;
    }

    public function setTemplateContent(array $content): self
    {
        $this->templateContent = $content;

        return $this;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function globalMergeVars(): array
    {
        return $this->globalMergeVars;
    }

    public function mergeVars(): array
    {
        return $this->mergeVars;
    }

    public function mergeLanguage(): string
    {
        return $this->mergeLanguage;
    }

    public function templateContent(): array
    {
        return $this->templateContent;
    }
}
