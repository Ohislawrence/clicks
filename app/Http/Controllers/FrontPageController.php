<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontPageController extends Controller
{
    /**
     * Display the homepage.
     */
    public function home()
    {
        return view('front.home');
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('front.about');
    }

    /**
     * Display the features page.
     */
    public function features()
    {
        return view('front.features');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('front.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: Implement contact form logic (send email, store in database, etc.)

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }

    /**
     * Display the for affiliates page.
     */
    public function forAffiliates()
    {
        return view('front.for-affiliates');
    }

    /**
     * Display the for advertisers page.
     */
    public function forAdvertisers()
    {
        return view('front.for-advertisers');
    }

    /**
     * Display the Store Builder landing page.
     */
    public function storeBuilder()
    {
        return view('front.store-builder');
    }

    /**
     * Display the FAQ page.
     */
    public function faq()
    {
        return view('front.faq');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return view('front.privacy');
    }

    /**
     * Display the terms of service page.
     */
    public function terms()
    {
        return view('front.terms');
    }
}
