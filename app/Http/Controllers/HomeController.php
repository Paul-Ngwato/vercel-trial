<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Wedding;

class HomeController extends Controller
{
    public function __invoke()
    {
        $wedding = $this->getWedding();

        $milestones = $wedding->storyMilestones()->orderBy('display_order')->get();
        $events     = $wedding->events()->active()->get();
        $infoItems  = $wedding->informationItems()->published()->orderBy('display_order')->get();
        $wishes     = \App\Models\GuestbookMessage::approved()->latest()->take(6)->get();
        $photos     = Photo::approved()->latest()->take(12)->get();

        return view('pages.home', compact(
            'wedding', 'milestones', 'events', 'infoItems', 'wishes', 'photos'
        ));
    }
}
