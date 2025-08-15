<?php
// File: app/View/Components/PhoneInput.php

namespace App\View\Components;

use Illuminate\View\Component;

class PhoneInput extends Component
{
    public $id;
    public $name;
    public $value;
    public $country;
    public $class;

    /**
     * Create a new component instance.
     *
     * @param string $id
     * @param string $name
     * @param string|null $value
     * @param string $country
     * @param string $class
     */
    public function __construct(string $id, string $name, string $value = null, string $country = 'co', string $class = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->country = $country;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.phone-input');
    }
}
