<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Rating extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

     /**
     * The facility code
     *
     * @var string
     */

    public $mflCode;


     /**
     * The search rating
     *
     * @var string
     */

    public $searchRating;

    /**
     * The search counter
     *
     * @var string
     */

     public $searchCounter;

     /**
     * Create the component instance.
     *
     * @param  string  $mflCode
     * @param  string  $searchRating
     * @param  string  $searchCounter
     * @return void
     */

    public function __construct($mflCode,$searchRating)
    {
        $this->mflCode = $mflCode;
        $this->searchRating = $searchRating;
        $this->searchCounter = $searchRating > 0 ? $searchRating : 5;
        // dd($mflCode);
    }

    public function isSelected($option)
    {
        return $option == $this->searchRating;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.rating');
    }
}
