<?php 
/**
 * Template Name: home
 */
?>

<?php 


$data = startup();

$page_id = get_the_ID();

$data['alternate_links'] = getAlternateLinks($page_id);

echo '<pre>';
var_dump($data);
echo '</pre>';


$page_info = $data['pages'][$page_id];

// Использование функции и получение пути к WebP-картинке
$image = convertWebp('image.jpg');

// Получите контент текущей страницы
$content = get_the_content();

?>

 <!-- <?php if ($lang === 'uk') { ?>
    UA
<?php  }elseif($lang === 'en'){?>
    EN
<?php  }elseif($lang === 'ru'){?>
    RU
 <?php  } ?> -->



 <!DOCTYPE html>
 <html lang="<?php echo $lang; ?>">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_info['title'] ?></title>
    <?php if($data['alternate_links']){ foreach ($data['alternate_links'] as $lang => $url) { ?>
        <link rel="alternate" hreflang="<?php echo $lang; ?>" href="<?php echo $url; ?>">
    <?php }}?>
    <link rel="stylesheet" href="/wp-content/themes/melis/assets/node_modules/normalize.css/normalize.css">
 </head>
 <body>
    <?php echo $content; ?>
    <img src="<?php echo $image; ?>" alt="">
 </body>
 </html>
 