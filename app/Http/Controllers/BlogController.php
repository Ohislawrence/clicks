<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index()
    {
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = BlogCategory::where('is_active', true)
            ->withCount('publishedPosts')
            ->orderBy('name')
            ->get();

        return view('front.blog.index', compact('posts', 'categories'));
    }

    /**
     * Display a single blog post.
     */
    public function show($slug)
    {
        $post = BlogPost::with(['category', 'author'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $post->incrementViews();

        // Get related posts
        $relatedPosts = BlogPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('front.blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Display posts by category.
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $category->publishedPosts()
            ->with('author')
            ->paginate(12);

        $categories = BlogCategory::where('is_active', true)
            ->withCount('publishedPosts')
            ->orderBy('name')
            ->get();

        return view('front.blog.category', compact('category', 'posts', 'categories'));
    }
}
