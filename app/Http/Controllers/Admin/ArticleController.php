<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Articles/Index', [
            'articles' => Article::query()->orderByDesc('published_at')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data=$this->validateData($request);
        if ($request->hasFile('featured_image')) $data['featured_image']=$request->file('featured_image')->store('articles','public');
        $data['slug']=$data['slug'] ?? \Illuminate\Support\Str::slug($data['title']);
        Article::create($data);
        return back()->with('success','Article created successfully.');
    }

    public function update(Request $request, Article $article)
    {
        $data=$this->validateData($request);
        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) Storage::disk('public')->delete($article->featured_image);
            $data['featured_image']=$request->file('featured_image')->store('articles','public');
        }
        $data['slug']=$data['slug'] ?? \Illuminate\Support\Str::slug($data['title']);
        $article->update($data);
        return back()->with('success','Article updated successfully.');
    }

    public function toggle(Article $article)
    {
        $article->update(['is_published'=>!$article->is_published]);
        return back()->with('success','Article status updated.');
    }

    public function destroy(Article $article)
    {
        if ($article->featured_image) Storage::disk('public')->delete($article->featured_image);
        $article->delete();
        return back()->with('success','Article deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'=>['required','string','max:255'],
            'slug'=>['nullable','string','max:255'],
            'category'=>['nullable','string','max:100'],
            'excerpt'=>['nullable','string','max:2000'],
            'content'=>['nullable','string'],
            'featured_image'=>['nullable','image','max:5120'],
            'published_at'=>['nullable','date'],
            'is_published'=>['nullable','boolean'],
            'sort_order'=>['nullable','integer'],
        ]);
    }
}
