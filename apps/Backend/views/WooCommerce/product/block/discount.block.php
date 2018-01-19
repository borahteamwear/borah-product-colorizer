<?php
/** @var $discountDTO \TBProductColorizerTM\DTO\DiscountDTO */
/** @var int $increment */
$domain = \TBProductColorizerTM\ProductColorizer::SLUG;
?>
<div class="options_group" data-discount="<?php echo $increment?>">
    <h4 class="options_group tb_wpc_padding">
        Discount <?php echo $increment?>

        <?php if (1 < $increment): ?>
            <small><a href="#" class="tb-wpc-remove-discount">remove</a></small>
        <?php endif?>
    </h4>

    <?php
    woocommerce_wp_text_input([
        'id'            => 'tb_wpc_data[discounts][' . $increment . '][minQuantity]',
        'label'         => __('Minimum Quantity : ' . $increment, $domain),
        'placeholder'   => __('Minimum Quantity : ' . $increment, $domain),
        'description'   => __('1', $domain),
        'default'       => '',
        'class'         => 'tb_wpc_form',
        'desc_tip'      => true,
        'value'         => (null !== $discountDTO) ? $discountDTO->getMinQuantity() : '',
    ]);

    woocommerce_wp_text_input([
        'id'            => 'tb_wpc_data[discounts][' . $increment . '][maxQuantity]',
        'label'         => __('Maximum Quantity : ' . $increment, $domain),
        'placeholder'   => __('Maximum Quantity : ' . $increment, $domain),
        'description'   => __('10', $domain),
        'default'       => '',
        'class'         => 'tb_wpc_form',
        'desc_tip'      => true,
        'value'         => (null !== $discountDTO) ? $discountDTO->getMaxQuantity() : '',
    ]);

    $type           = '';
    $isScheduled    = ($discountDTO && (null !== $discountDTO->getStartDate() || null !== $discountDTO->getEndDate()));


    if (!$isScheduled)
    {
        $scheduleLink  = ' <a href="#" class="discount_schedule" data-target="' . $increment . '">Schedule</a>';
        $scheduleLink .= ' <a href="#" class="cancel_discount_schedule hidden" data-target="' . $increment . '">Cancel Schedule</a>';
    }
    else
    {
        $scheduleLink  = ' <a href="#" class="discount_schedule hidden" data-target="' . $increment . '">Schedule</a>';
        $scheduleLink .= ' <a href="#" class="cancel_discount_schedule" data-target="' . $increment . '">Cancel Schedule</a>';
    }

    if ($discountDTO && $discountDTO->getPrice())
    {
        $type = 'fixed';
    }
    elseif ($discountDTO && $discountDTO->getPercentage())
    {
        $type = 'percentage';
    }

    woocommerce_wp_radio([
        'id'            => 'tb_wpc_discount_manager_type_' . $increment,
        'label'         => __('Type for ' . $increment, $domain),
        'value'         => $type,
        'class'         => 'tb_wpc_type_selector',
        'options'       => [
            'fixed'         => __('Fixed', $domain),
            'percentage'    => __('Percentage', $domain),
        ],
    ])
    ?>

    <div class="tb_wpc_type_container<?php if ('fixed' !== $type) echo ' hidden'?>" data-type="fixed">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[discounts][' . $increment . '][price]',
            'label'         => __('Price : ' . $increment, $domain) . $scheduleLink,
            'placeholder'   => __('Price : ' . $increment, $domain),
            'description'   => __('10.20', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => (null !== $discountDTO) ? $discountDTO->getPrice() : '',
        ]);
        ?>
    </div>

    <div class="tb_wpc_type_container<?php if ('percentage' !== $type) echo ' hidden'?>" data-type="percentage">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[discounts][' . $increment . '][percentage]',
            'label'         => __('Percentage : ' . $increment, $domain) . $scheduleLink,
            'placeholder'   => __('Percentage : ' . $increment, $domain),
            'description'   => __('20', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form',
            'desc_tip'      => true,
            'value'         => (null !== $discountDTO) ? $discountDTO->getPercentage() : '',
        ]);
        ?>
    </div>

    <div class="form-field discount_price_dates_fields<?php if (!$isScheduled) echo ' hidden'?>" data-target="<?php echo $increment?>">
        <?php
        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[discounts][' . $increment . '][startDate]',
            'label'         => __('Start Date for Discount ' . $increment, $domain),
            'placeholder'   => __('From / Start Date…  YYYY-MM-DD for discount ' . $increment, $domain),
            'description'   => __('In YYYY-MM-DD format', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form hasDatepicker',
            'custom_attributes' => [
                'maxlength' => '10',
                'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
            ],
            'desc_tip'      => true,
            'value'         => (null !== $discountDTO && null !== $discountDTO->getStartDate()) ? $discountDTO->getStartDate()->format('Y-m-d') : '',
        ]);

        woocommerce_wp_text_input([
            'id'            => 'tb_wpc_data[discounts][' . $increment . '][endDate]',
            'label'         => __('End Date for Discount ' . $increment, $domain),
            'placeholder'   => __('To / End Date…  YYYY-MM-DD for discount ' . $increment, $domain),
            'description'   => __('In YYYY-MM-DD format', $domain),
            'default'       => '',
            'class'         => 'tb_wpc_form hasDatepicker',
            'custom_attributes' => [
                'maxlength' => '10',
                'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
            ],
            'desc_tip'      => true,
            'value'         => (null !== $discountDTO && null !== $discountDTO->getEndDate()) ? $discountDTO->getEndDate()->format('Y-m-d') : '',
        ])
        ?>
    </div>
</div>