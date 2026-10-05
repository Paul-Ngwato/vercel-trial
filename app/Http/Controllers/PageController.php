<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * The site is now a single scrolling page. Old URLs simply
     * drop the guest at the right section on the homepage.
     */
    public function story()
    {
        return redirect()->to(route('home') . '#story', 302);
    }

    public function invitation()
    {
        return redirect()->to(route('home') . '#invitation', 302);
    }

    public function schedule()
    {
        return redirect()->to(route('home') . '#schedule', 302);
    }

    public function location()
    {
        return redirect()->to(route('home') . '#location', 302);
    }

    public function information()
    {
        return redirect()->to(route('home') . '#information', 302);
    }
}
