<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{

    private $iconMap = [
        'user' => 'iconyonghu',
        'email' => 'iconemail',
        'password' => 'iconicon2',
    ];
    
    public $type;
    public $placeholder;
    public $class;
    public $icon;

    public function __construct($type = 'user', $placeholder = '', $class = '')
    {
        $this->placeholder = $placeholder;
        $this->icon = array_key_exists($type, $this->iconMap)
            ? $this->iconMap[$type]
            : $this->iconMap['user'];

        $this->type = $type;
        $this->class = $class;
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
