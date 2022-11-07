{$png={image file="{$picture.filename}.png" module=$picture.module}}
{$pngHiDpi={image file="{$picture.filename}@2x.png" module=$picture.module}}
{$webp={image file="{$picture.filename}.webp" module=$picture.module}}
{$webpHiDpi={image file="{$picture.filename}@2x.webp" module=$picture.module}}
<picture>
    <source type="image/webp"
            srcset="{$webp} 1x{if !empty($webpHiDpi)}, {$webpHiDpi} 2x{/if}">
    <img src="{$png}"
         srcset="{$png} 1x{if !empty($pngHiDpi)}, {$pngHiDpi} 2x{/if}"
         {if isset($picture.class)}class="{$picture.class}"{/if}
            {if isset($picture.alt)}alt="{$picture.alt}"{/if}
            {if isset($picture.attributes)}{$picture.attributes}{/if}>
</picture>
