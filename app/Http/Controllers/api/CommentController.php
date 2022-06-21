<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Blog;
use App\Models\Comment;
use App\Notifications\CommentNotification;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Blog $blog
     * @return \Illuminate\Http\Response
     */
    public function index(Blog $blog)
    {
        $comments = $blog->Comments()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $comments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->blog_id = $request->blog_id;
        $comment->reviewer_id = auth()->id();
        $comment->save();

        $blog = Blog::find($request->blog_id);
        Notification::send($blog->Author, new CommentNotification($comment->id));
        return response()->json([
            'message'       =>  'Comment created successfully',
            'comment_id'    =>  $comment->id
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return response()->json([
            'data'  =>  $comment
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  CommentRequest  $request
     * @param  Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        if (auth()->id() != $comment->reviewer_id) {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }

        $comment->content = $request->content;
        $comment->save();
        return response()->json([
            'message'       =>  'Comment edited successfully',
            'comment_id'    =>  $comment->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if (auth()->id() != $comment->reviewer_id) {
            return response()->json([
                'message' => 'Not authorized'
            ], 401);
        }

        $comment->delete();
        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
