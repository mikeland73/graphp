<p>
  <?=$post->getText()?>
</p>
<label>Comments:</label>
<ul>
  <?php foreach ($post->getComments() as $comment):?>
    <li><?=$comment->getText()?></li>
  <?php endforeach ?>
</ul>
<form method="POST" action="<?=Posts::URI()->createComment()?>">
  <label>New Comment:</label>
  <textarea name="text"></textarea>
  <button>Create</button>
  <input type="hidden" name="post_id" value="<?=$post->getID()?>">
  <?=GPSecurity::csrf()?>
</form>