<?php $this->extend('a.php'); ?>

<?php $this->block('content'); ?>
<h1><?= $this->name ?></h1>
<h2><?= $this->age ?></h2>

<ul>
<?php foreach($this->friends as $friend) : ?>
	<li><?= $friend; ?></li>
<?php endforeach; ?>
</ul>
<?php $this->endblock(); ?>

<?php $this->block('footer'); ?>
<footer>Test</footer>
<?php $this->endblock(); ?>

<div>Last</div>
