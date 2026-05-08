<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component
{
    public $title;

    public function __construct($title = 'Dashboard')
    {
        $this->title = $title;
    }

    public function render()
    {
        return view('layouts.admin');
    }
}
