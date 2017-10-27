<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\FileableController;
use App\Http\Repositories\PostRepository;
use App\Post;
use Illuminate\Http\Request;
use XblogConfig;

class HomeController extends Controller
{
    protected $postRepository;
    protected $fileableController;

    /**
     * Create a new controller instance.
     *
     * @param PostRepository $postRepository
     * @param FileableController $fileableController
     */
    public function __construct(PostRepository $postRepository, FileableController $fileableController)
    {
        $this->postRepository = $postRepository;
        $this->fileableController = $fileableController;
    }

    public function index()
    {
        $this->fileableController->syncAll();
        return view('index');
    }

    public function search(Request $request)
    {
        $key = trim($request->get('q'));
        if ($key == '')
            return back()->withErrors("请输入关键字");
        $page_size = XblogConfig::getValue('page_size', 7);
        $key = "%$key%";
        $posts = Post::where('title', 'like', $key)
            ->orWhere('description', 'like', $key)
            ->with(['tags', 'category'])
            ->withCount('comments')
            ->orderBy('view_count', 'desc')
            ->paginate($page_size);
        $posts->appends($request->except('page'));
        return view('search', compact('posts'));
    }

    public function projects()
    {
        return view('projects');
    }

    public function achieve()
    {
        $posts = $this->postRepository->achieve();
        $posts_count = $this->postRepository->postCount();
        return view('achieve', compact('posts', 'posts_count'));
    }

}
