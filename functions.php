<?php 


function start_session() {
    if ( ! session_id() ) {
        session_start();
    }
}

add_action( 'init', 'start_session', 1 );


// function autoRedirectWithLanguageSlug() {
//     if (session_id()) {
//         $currentLang = getLang(); 

//         if ($currentLang && in_array($currentLang, array('uk', 'en'))) {
//             $url = $_SERVER['REQUEST_URI'];
//             if (!preg_match('/^(\/ua\/|\/en\/)/', $url)) {
//                 wp_redirect('/' . $currentLang . $url);
//                 exit();
//             }
//         }
//     }
// }


// add_action('template_redirect', 'autoRedirectWithLanguageSlug');



// $source_post_id = 16;

// $current_language = 'en';

// $translations = pll_get_post_translations($source_post_id);

// $alternative_post_id = $translations[$current_language];


function getLangFromURL() {
    $url = $_SERVER['REQUEST_URI'];
    $parts = explode('/', $url);

    $lang = $parts[1];
    if ($lang && in_array($lang, array('uk', 'en', 'ru')) && $lang !== 'favicon.ico') {
        return $lang;
    } else {
        return '';
    }
}

function getLang() {
    $slug = getLangFromURL();

    if (!empty($slug)) {
        $_SESSION['lang'] = $slug;
        return $slug;
    } elseif (!empty($_SESSION['lang'])) {
        return $_SESSION['lang'];
    } else {
        $_SESSION['lang'] = 'uk';
        return 'ua';
    }
}

function addLink($link) {
    $currentLang = getLang();

    $allowedLanguages = array('uk', 'en', 'ru');

    if (in_array($currentLang, $allowedLanguages) && !empty($currentLang)) {
        $link = '/' . $currentLang . $link;
    }

    return $link;
}


function seo_link($post_id, $lang) {
    
    $translated_post_id = pll_get_post($post_id, $lang);

    if ($translated_post_id) {
        $link = get_permalink($translated_post_id);
    }

    return $link;
}

function switchLang($lang_code, $post_id = null) {

    if(!empty($lang_code)){

        $_SESSION['lang'] = $lang_code;

    }else{
        $lang_session = $_SESSION['lang'];

        $_SESSION['lang'] = $lang_session;
    }

}

function getAlternateLinks($post_id){
    $languages = pll_languages_list();
    $alternative_links = array(); // Создаем массив для хранения альтернативных ссылок
    
    foreach ($languages as $lang) {
        $alternative_post_id = pll_get_post($post_id, $lang);
        
        // Создаем ссылку на альтернативную версию и сохраняем ее в массив
        $alternative_links[$lang] = get_permalink($alternative_post_id);
    }
    
    return $alternative_links; // Возвращаем массив альтернативных ссылок
}



function startup() {

    $lang = getLang();

    $data = array();

    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'page',
    );

    $posts = get_posts($args);

    $links = array();

    if ($posts) {
        $all_posts = array();

        foreach ($posts as $post) {
            $post_id = $post->ID;
            
            $all_posts[] = $post_id ;
        }


        foreach ($all_posts as $post_id ){

            $title = get_the_title($post_id);

            $links['pages'][$post_id] = array(
                'page_id' => $post_id,
                'meta_title' => $title,
                'title' => $title,
                'link' => seo_link($post_id, $lang)
            );
        }

    }

    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'post',
    );

    $posts = get_posts($args);

    if ($posts) {
        $all_posts = array();

        foreach ($posts as $post) {
            $post_id = $post->ID;
            
            $all_posts[] = $post_id ;
        }

        foreach ($all_posts as $post_id ){

            $title = get_the_title($post_id);

            $links['posts'][$post_id] = array(
                'post_id' => $post_id,
                'meta_title' => $title,
                'title' => $title,
                'link' => seo_link($post_id, $lang)
            );

        }


        }

        $data = $links;
        
        return $data;

}

function convertWebp($image_src) {
    // Получите абсолютный серверный путь к исходному изображению
    $image_path = get_template_directory() . '/images/' . $image_src;

    if (empty($image_path) || !file_exists($image_path)) {
        return ''; // Вернем пустую строку или другое значение по умолчанию
    }
  
    // Определите относительный путь к папке вашей темы внутри директории загрузки
    $theme_dir = get_template_directory_uri() . '/images/webp/';

    // Определите имя файла и расширение
    $image_filename = basename($image_path);
    $webp_filename = pathinfo($image_filename, PATHINFO_FILENAME) . '.webp';

    // Определите относительный путь и имя файла WebP
    $webp_file = str_replace(ABSPATH, '', $theme_dir) . $webp_filename;

    // Проверьте, существует ли файл WebP, и если да, верните его путь
    if (file_exists(ABSPATH . $theme_dir . $webp_filename)) {
        return $webp_file;
    }

    // Создайте объект Imagick с исходным изображением
    $image = new Imagick($image_path);
    $image->setFormat('webp');

    // Проверьте, существует ли папка, и если нет, создайте ее
    if (!file_exists(ABSPATH . $theme_dir)) {
        wp_mkdir_p(ABSPATH . $theme_dir);
    }

    // Сохраните изображение в формате WebP
    $image->writeImage(ABSPATH . $theme_dir . $webp_filename);

    // Верните относительный путь к созданной WebP-картинке
    return $webp_file;
}




