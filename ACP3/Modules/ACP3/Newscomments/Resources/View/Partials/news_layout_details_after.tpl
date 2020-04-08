<section>
    {load_module module="frontend/comments" args=['module' => 'news', 'entryId' => $news.id]}
    {load_module module="frontend/comments/index/create" args=['module' => 'news', 'entryId' => $news.id, 'redirectUrl' => base64_encode("news/index/details/id_`$news.id`")]}
</section>
