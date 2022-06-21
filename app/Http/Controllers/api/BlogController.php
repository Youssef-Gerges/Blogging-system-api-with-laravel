<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::whereStatus('published')->orderBy('created_at', 'desc')->with([
            'category' => function ($query) {
                $query->select('id', 'name');
            }, 'author' => function ($query) {
                $query->select('id', 'name');
            }
        ])->paginate(10);
        return response()->json([
            'data' => $blogs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogRequest $request)
    {
        $user = User::find(auth()->user()->id);
        if ($user->tokenCan('author')) {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->body = $request->body;
        $blog->category_id = $request->category_id;
        $blog->author_id = $user->id;
        $blog->save();

        return response()->json([
            'message' => 'Blog created successfully',
            'blog_id' => $blog->id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        return response()->json([
            'data' => $blog
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  BlogRequest  $request
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(BlogRequest $request, Blog $blog)
    {
        if ($blog->author_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }

        $blog->title = $request->title;
        $blog->body = $request->body;
        $blog->category_id = $request->category_id;
        $blog->save();

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog_id' => $blog->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        if ($blog->author_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }

        $blog->delete();
        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }
}
