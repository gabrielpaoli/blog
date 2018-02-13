<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Illuminate\Support\Facades\Auth;
use App\LolApi\riotapi;

class ArticleController extends Controller
{

    private function parseArticle($articles)
    {
        foreach ($articles as $key => $article) {
            $user = Auth::loginUsingId($article->user_id);
            $article->setAttribute('user_name', $user->name);
        }
    }


    /**
     * return user id.
     *
     * @return userId
     */

    private function userId()
    {
        return Auth::user()->id;
    }


    /**
     * return if article is public.
     * 
     * @param int $id
     * @return isPublic
     */

    private function isPublic($id)
    {
        $article = Article::find($id);
        if($article->public === 0):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * return if user is author.
     * 
     * @param int $id
     * @return isAuthor
     */

    private function isAuthor($id)
    {
        $article = Article::find($id);
        if(Auth::user()->id === $article->user_id):
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::where('user_id', '=', $this->userId())->latest()->paginate(10);
        $this->parseArticle($articles);
        return view('articles.index',compact('articles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Display a listing of all articles.
     *
     * @return All articles
     */
    public function allArticles()
    {
        $articles = Article::where('public', '=', 0)->latest()->paginate(10);
        $this->parseArticle($articles);
        return view('articles.index',compact('articles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = $this->userId();
        return view('articles.create',compact('userId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
        Article::create($request->all());
        return redirect()->route('articles.index')
                        ->with('success','Article created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {        
        $article = Article::find($id);
        if($this->isPublic($id) or $this->isAuthor($id))
            return view('articles.show',compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = $this->userId();
        $article = Article::find($id);
        if($this->isAuthor($id))        
            return view('articles.edit',compact('article', 'userId'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
        Article::find($id)->update($request->all());
        return redirect()->route('articles.index')
                        ->with('success','Article updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->isAuthor($id)):        
            Article::find($id)->delete();
            return redirect()->route('articles.index')
                            ->with('success','Article deleted successfully');
        endif;                    
    }
}