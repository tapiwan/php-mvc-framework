<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?= $this->title; ?></title>
</head>
<body>
<?php $this->block('header'); ?>
<header>BASE HEADER</header>
<?php $this->endblock(); ?>

<?php $this->block('content'); ?>
<main>BASE CONTENT</main>
<?php $this->endblock(); ?>

<?php $this->block('footer'); ?>
<footer>BASE FOOTER</footer>
<?php $this->endblock(); ?>
</body>
</html>