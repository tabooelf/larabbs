<?php

namespace App\Observers;

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
}