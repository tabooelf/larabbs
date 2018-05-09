<?php

namespace App\Observers;

use App\Jobs\TranslateSlug;
use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }
    public function saving(Topic $topic){
        //simdtor 自带转义储存可以不过勒
        // $topic->body = clean($topic->body, 'default');
        $topic->excerpt = make_excerpt($topic->body);
    }
    public function saved(Topic $topic){
        // 如slug字段无内容, 即使用翻译器对title进行翻译
        if( !$topic->slug ){
            dispatch(new TranslateSlug($topic));
        }
    }
}