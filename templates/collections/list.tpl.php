<html>
<body>
<h1>Collections</h1>
<ul>
  <? foreach($collections as $collection): ?>
    <li><a href="index.php?q=/collections<?= $collection->alias ?>"><?= $collection->name ?></a></li>
  <? endforeach; ?>
</ul>
</body>
</html>