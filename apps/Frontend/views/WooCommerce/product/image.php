<?php
/** @var string $html */
/** @var \TBProductColorizerTM\DTO\Collection\WCProductTemplateCollection $templates */
?>
<div id="tb_svg_container" class="tb_wpc_templates">
    <?php
    $i = 0;
    /** @var \TBProductColorizerTM\DTO\WCProductTemplateDTO $template */
    foreach ($templates as $template):
        $images = $this->getTemplateImages($template);
        ?>
        <div id="template_<?php echo $template->getTemplateId()?>" class="tb_wpc_template_container">
            <?php
            /** @var \TBProductColorizerTM\DTO\ImageDTO $image */
            foreach ($images as $image):
                ?>
                <img id="tb_wpc_overlay_<?php echo $template->getTemplateId() . '_' . $i?>" src="<?php echo $image->getUrl()?>" data-listen="<?php echo $image->getTargetInput()?>" class="tb_wpc_svg_convert">
            <?php endforeach?>
        </div>
        <?php
        $i++;
    endforeach;
    ?>
    <div class="tb_wpc_text_center">
        Don't forget to choose your colors
    </div>
</div>