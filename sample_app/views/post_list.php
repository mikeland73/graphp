<form method="POST" action="<?=Posts::getURI('create')?>">
  <label>New Post:</label>
  <textarea name="text"></textarea>
  <button>Create</button>
  <?=GPSecurity::csrf()?>
</form>
<ul>
  <?php foreach ($posts as $post):?>
    <li>
      <a href="<?=Posts::getURI('one', $post->getID())?>">
        <?=$post->getText()?>
      </a>
    </li>
  <?php endforeach ?>
</ul>