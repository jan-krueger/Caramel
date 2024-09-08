<?php

namespace Caramel\Controller;

use Caramel\Core\Http\Request;
use Caramel\Core\Verifier\Verifier;
use Caramel\Model\Post;

class PostController extends Controller
{

    public function index()
    {
        return $this->response('posts.index', [
            'heading' => 'Posts',
            'posts' => Post::all(),
        ]);
    }

    public function show(int $id)
    {
        $post = Post::find($id);

        return $this->response('posts.show', [
            'heading' => $post->title,
            'post' => $post,
        ]);
    }

    public function create()
    {
        return $this->response('posts.create', [
            'heading' => "Create a new post", 
        ]);
    }

    public function store(Request $request)
    {

        Verifier::verify($request, [
            'body' => 'required|min:100|max:500',
        ])->validateOrFail();


        dd($request->post_data);
        return $this->redirect('/posts');
    }

}