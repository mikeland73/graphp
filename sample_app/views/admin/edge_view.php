<h1>Edges</h1>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th scope="col" class="col-xs-4">Type</th>
        <th scope="col" class="col-xs-4">Name</th>
        <th scope="col" class="col-xs-4">From Type</th>
        <th scope="col" class="col-xs-4">To Type</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($edges as $edge): ?>
        <tr>
          <th scope="row"><code><?=$edge->getType()?></code></th>
          <th scope="row"><?=$edge->getName()?></th>
          <th scope="row"><?=GPNodeMap::getCLass($edge->getFromType())?></th>
          <th scope="row"><?=GPNodeMap::getClass($edge->getToType())?></th>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>