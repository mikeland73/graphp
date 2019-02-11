<?php

final class Posts extends GPController {

  public function __construct() {
    if (!GPSession::get('user_id')) {
      Welcome::redirect();
    }
  }

  public function index() {
    GP::view('post_list', ['posts' => Post::getAll()]);
  }

  public function one($id) {
    $post = Post::getByID($id);
    if (!$post) {
      GP::return404();
    }
    $post->loadComments();
    GP::view('one_post', ['post' => $post]);
  }

  public function create() {
    $text = $this->post->getString('text');
    $post = (new Post())->setText($text)->save();
    $post->addCreator(User::getByID(GPSession::get('user_id')))->save();
    Posts::redirect();
  }

  public function createComment() {
    $text = $this->post->getString('text');
    $post_id = $this->post->getString('post_id');
    $post = Post::getByID($post_id);
    $comment = (new Comment())->setText($text)->save();
    $comment->addCreator(User::getByID(GPSession::get('user_id')))->save();
    $post->addComments($comment)->save();
    Posts::redirect()->one($post_id);
  }
}
