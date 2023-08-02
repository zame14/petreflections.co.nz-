<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/15/2022
 * Time: 11:25 AM
 */
global $post;
$pet = new Pet($post->ID);
$owner = $pet->getOwner();
$vet = $pet->getClinic();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="image-wrapper">
                <?=$pet->getFeatureImage()?>
                <div class="pet-name">
                    <?=$pet->getCustomField('pet-name')?>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-8 pet-info div-spacer">
            <h1>TAG ID - <?=$pet->getCustomField('pet-tag-id')?></h1>
            <div><label>Pet Type:</label><?=ucfirst($pet->getPetType())?></div>
            <div><label>Weight (kg):</label><?=$pet->getCustomField('pet-weight')?></div>
            <div><label>Age:</label><?=$pet->getCustomField('pet-age')?></div>
            <div class="vet-clinic-wrapper">
                <label>Vet Clinic:</label><?=$vet->getTitle()?>
            </div>
            <div class="current-status">
                <label>Current status</label><?=$pet->getCustomField('pet-status')?>
            </div>
        </div>
    </div>
    <div class="row content-wrapper">
        <div class="col-12 col-md-6 div-spacer">
            <h3 class="label">Owner Information</h3>
            <div class="owner-name"><?=$owner->getTitle()?></div>
            <div><label>Ph:</label><?=$owner->getCustomField('customer-phone')?></div>
            <div><label>Email:</label><?=$owner->getCustomField('customer-email')?></div>
            <div><label>Address:</label><?=$owner->getCustomField('customer-address')?></div>
            <?php
            if($pet->getCustomField('pet-ashes-returned') == "yes") { ?>
                <div class="notify-by"><label>Notify owner via - </label><?=ucfirst($owner->getNotifyBy())?></div>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 div-spacer">
            <h3 class="label">Ashes Information</h3>
            <div><label>Return Ashes:</label><?=ucfirst($pet->getCustomField('pet-ashes-returned'))?></div>
            <?php
            if($pet->getCustomField('pet-ashes-returned') == "yes") { ?>
                <div><label>Returned in:</label><?=$pet->getReturnedIn()?></div>
                <?php
                if($pet->getCustomField('pet-ashes-returned-in') == "urn") { ?>
                    <div><label>Urn Size:</label><?=$pet->getCustomField('wooden-urn-size')?></div>
                    <?php
                }
                ?>
                <div><label>Memorial Jewellery:</label><?=$pet->getJewellery()?></div>
                <div class="return-to"><label>Return to:</label><?=$pet->getReturnTo()?></div>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 button-wrapper">
            <a href="<?=get_page_link(29)?>"><span class="fa fa-angle-left"></span> Back to My Account</a>
        </div>
    </div>
</article>