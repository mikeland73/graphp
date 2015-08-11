<form method="POST" action="<?=Posts::URI()->create()?>">
  <label>New Post:</label>
  <textarea name="text"></textarea>
  <button>Create</button>
  <?=GPSecurity::csrf()?>
</form>
<ul>
  <?php foreach ($posts as $post):?>
    <li>
      <a href="<?=Posts::URI()->one($post->getID())?>">
        <?=StringLibrary::truncate($post->getText(), 10)?>
      </a>
    </li>
  <?php endforeach ?>
</ul>