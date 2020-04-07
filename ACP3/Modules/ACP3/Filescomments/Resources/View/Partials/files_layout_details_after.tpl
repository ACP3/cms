<section>
    {load_module module="frontend/comments" args=['module' => 'files', 'entryId' => $file.id]}
    {load_module module="frontend/comments/index/create" args=['module' => 'files', 'entryId' => $file.id, 'redirectUrl' => base64_encode("files/index/details/id_`$file.id`")]}
</section>
