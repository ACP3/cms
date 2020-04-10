<section>
    {load_module module="frontend/comments" args=['module' => 'gallery', 'entryId' => $gallery_picture.id]}
    {load_module module="frontend/comments/index/create" args=['module' => 'gallery', 'entryId' => $gallery_picture.id, 'redirectUrl' => base64_encode("gallery/index/details/id_`$gallery_picture.id`")]}
</section>
