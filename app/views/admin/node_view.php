<h1>Node View - <?= $node->getID() ?></h1>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th scope="col" class="col-xs-4">Key</th>
        <th scope="col" class="col-xs-4">Value</th>
        <th scope="col" class="col-xs-4">Indexed</th>
      </tr>
    </thead>
    <tbody>
      <? foreach($node->getDataArray() as $key => $value): ?>
        <tr>
          <th scope="row"><code><?=$key?></code></th>
          <th scope="row"><?=$value?></th>
          <th scope="row">Yes</th>
        </tr>
      <? endforeach; ?>
    </tbody>
  </table>
</div>