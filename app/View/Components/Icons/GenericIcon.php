<?php

namespace App\View\Components\Icons;

use Illuminate\View\Component;

class GenericIcon extends Component
{
    /**
     * The button type.
     *
     * @var string
     */
    public $type;

    /**
     * The button title.
     *
     * @var string
     */
    public $title;

    /**
     * The button color.
     *
     * @var string
     */
    public $color;

    /**
     * The button hover color.
     *
     * @var string
     */
    public $hoverColor;

    /**
     * The button icon SVG path.
     *
     * @var string
     */
    public $iconPath;

    /**
     * Create a new component instance.
     *
     * @param string $type
     * @param string $title
     * @param string $color
     * @param string $hoverColor
     * @param string $iconPath
     * @return void
     */
    public function __construct(
        $type = 'button',
        $title = '',
        $color = 'blue-500',
        $hoverColor = 'blue-700',
        $iconPath = ''
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->color = $color;
        $this->hoverColor = $hoverColor;
        $this->iconPath = $iconPath;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.buttons.icon-button');
    }
}
