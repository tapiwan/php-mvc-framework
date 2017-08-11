<h1><?= $this->name; ?></h1>
<h1><?= $this->blub; ?></h1>
<ul>
    <? foreach($this->friends as $friend) : ?>
        <li><?= $friend; ?></li>
    <? endforeach; ?>
</ul>

