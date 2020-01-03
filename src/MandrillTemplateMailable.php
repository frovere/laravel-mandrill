<?php

namespace Felrov\Drill;

use Illuminate\Mail\Mailable;

abstract class MandrillTemplateMailable extends Mailable
{
    private $template;

    public function __construct(MandrillTemplate $template)
    {
        $this->template = $template;
    }

    public function build()
    {
        return $this->html(\serialize($this->template));
    }
}
