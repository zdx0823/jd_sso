<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{

    private $iconMap = [
        'name' => 'iconyonghu',
        'email' => 'iconemail',
        'password' => 'iconicon2',
    ];
    
    public $type;
    public $placeholder;
    public $class;
    public $icon;
    public $name;
    public $rule;
    public $jshook;

    public function __construct(
        $class = '',
        $placeholder = '',
        $type = 'name',
        $name = "default",
        $rule = '',
        $jshook = ''
    )
    {
        $this->placeholder = $placeholder;
        $this->icon = array_key_exists($type, $this->iconMap)
            ? $this->iconMap[$type]
            : $this->iconMap['name'];

        $this->type = $type;
        $this->class = $class;
        $this->name = $name;
        $this->rule = $rule;
        $this->jshook = $jshook;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}
