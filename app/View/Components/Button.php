<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{

    public $bgColor;
    public $focusBgColor;
    public $value;
    public $class;

    private $bgColorMap = [
        'primary' => 'bg-blue-500',
        'danger' => 'bg-red-500',
        'success' => 'bg-green-500',
        'info' => 'bg-gray-500',
    ];

    private $focusBgColorMap = [
        'primary' => 'focus:bg-blue-600 focus:bg-blue-700',
        'danger' => 'hover:bg-red-600 focus:bg-red-700',
        'success' => 'focus:bg-green-600 focus:bg-green-700',
        'info' => 'focus:bg-gray-600 focus:bg-gray-700',
    ];

    public function __construct($type = 'primary', $value = "按钮", $class = '')
    {
        $this->bgColor = array_key_exists($type, $this->bgColorMap)
            ? $this->bgColorMap[$type]
            : $this->bgColorMap['primary'];

        $this->focusBgColor = array_key_exists($type, $this->focusBgColorMap)
            ? $this->focusBgColorMap[$type]
            : $this->focusBgColorMap['primary'];

        $this->value = $value;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
