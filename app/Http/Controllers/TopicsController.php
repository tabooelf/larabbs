<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Topic $topic, Request $request)
	{
		$topics = Topic::withOrder($request->order)->paginate();
		return view('topics.index', compact('topics'));
	}

    public function show(Request $request, Topic $topic)
    {
        if( !empty($topic->slug) && $topic->slug != $request->slug ){
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
        $topic->fill($request->all());
        $topic->user_id = Auth::id() ;
        $topic->save();
        // var_dump($topic);

		return redirect()->to($topic->link())->with('success', '创建成功.');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '更新成功.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功.');
	}

    public function uploadImage(Request $request, ImageUploadHandler $uploader){
        // 默认返回失败数据
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        if($request->upload_file){
            $res = $uploader->save($request->upload_file, 'topic', \Auth::id(),1024);
            if($res){
                $data['success'] = true;
                $data['msg'] = '上传成功';
                $data['file_path'] = $res['path'];
            }
        }
        return $data;
    }
}