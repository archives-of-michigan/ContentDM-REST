<html>
<body>
<h1>Collections</h1>
<ul>
  <? foreach($collections as $collection): ?>
    <li><a href="<?= $collection->alias ?>"><?= $collection->name ?></a></li>
  <? endforeach; ?>
</ul>
</body>
</html>