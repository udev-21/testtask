<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPost extends Mailable
{
    use Queueable, SerializesModels;
    protected $post;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('post', [
            'title'=>$this->post->title,
            'author'=>$this->post->author,
            'content'=>$this->post->content,
        ]);
    }
}
