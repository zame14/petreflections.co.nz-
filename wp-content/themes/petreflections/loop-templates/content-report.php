<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 10/20/2022
 * Time: 11:51 AM
 */
global $post;
$i = 0;
$exclude = array(1);
$args = array(
    'exclude' => $exclude,
    'orderby' => 'title',
    'order' => 'ASC',
    'fields' => 'all'
);
$users = get_users($args);
$start_date_val = '';
$end_date_val = '';
$filter_by_date = false;
if(isset($_REQUEST['start_date']) && $_REQUEST['start_date'] <> "") {
    $start_date_val = $_REQUEST['start_date'];
    $filter_by_date = true;
}
if(isset($_REQUEST['end_date']) && $_REQUEST['end_date'] <> "") {
    $end_date_val = $_REQUEST['end_date'];
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="date-picker-wrapper">
        <form method="get" action="<?=get_page_link($post->ID)?>" id="pet-report">
            <div class="inner-wrapper">
                <div>
                    <label>Start date:</label>
                    <input class="datepicker1" data-provide="datepicker1" name="start_date" value="<?=$start_date_val?>" />
                </div>
                <div>
                    <label>End date:</label>
                    <input class="datepicker2" data-provide="datepicker2" name="end_date" value="<?=$end_date_val?>" />
                </div>
                <div>
                    <a href="javascript:;" class="btn btn-primary">View report</a>
                </div>
                <div>
                    <a href="<?=get_page_link($post->ID)?>" class="clear">clear</a>
                </div>
            </div>
        </form>
    </div>
    <div class="reports-wrapper">
        <table class="table report">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Clinic name</th>
                <th class="center-me">No. of pets</th>
                <th class="center-me">Urns</th>
                <th class="center-me">Jewellery</th>
                <th class="center-me">Returned to vets</th>
                <th class="center-me">Returned home</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($users as $user) {
            $vet = getVetByUserID($user->ID);
            $num_pets = 0;
            $num_urns = 0;
            $num_jewellery = 0;
            $num_pets_to_clinic = 0;
            $num_pets_to_home = 0;
            if(count($vet) > 0 ) {
                if ($filter_by_date) {
                    $date1 = convertToRawDate($_REQUEST['start_date']);
                    $date2 = convertToRawDate($_REQUEST['end_date']);
                    $num_pets = $vet[0]->report_num_pets_by_date($date1, $date2);
                    $num_urns = $vet[0]->report_num_of_by_date('wpcf-pet-memorial-jewellery', 'yes', $date1, $date2);
                    $num_jewellery = $vet[0]->report_num_of_by_date('wpcf-pet-memorial-jewellery', 'yes', $date1, $date2);
                    $num_pets_to_clinic = $vet[0]->report_num_of_by_date('wpcf-pet-delivery-options', 'clinic', $date1, $date2);
                    $num_pets_to_home = $vet[0]->report_num_of_by_date('wpcf-pet-delivery-options', 'address', $date1, $date2);

                } else {
                    $num_pets = $vet[0]->report_num_pets();
                    $num_urns = $vet[0]->report_num_of('wpcf-pet-memorial-jewellery', 'yes');
                    $num_jewellery = $vet[0]->report_num_of('wpcf-pet-memorial-jewellery', 'yes');
                    $num_pets_to_clinic = $vet[0]->report_num_of('wpcf-pet-delivery-options', 'clinic');
                    $num_pets_to_home = $vet[0]->report_num_of('wpcf-pet-delivery-options', 'address');
                }

                echo '<tr>
                    <td class="center-me"><span class="fa fa-plus table-btn" onclick="showTable(' . $i . ')"></span></td>
                    <td>' . $vet[0]->getTitle() . '</td>
                    <td class="center-me num-of-pets">' . $num_pets . '</td>
                    <td class="center-me">' . $num_urns . '</td>
                    <td class="center-me">' . $num_jewellery . '</td>
                    <td class="center-me">' . $num_pets_to_clinic . '</td>
                    <td class="center-me">' . $num_pets_to_home . '</td>
                </tr>
                <tr class="row-collapse row-collapse-' . $i . '">
                    <td colspan="7" class="td-no-padding">
                        <table class="table ntable">
                            <thead>
                            <tr>
                                <th>Pet Tag ID</th>
                                <th>Pet Type</th>
                                <th class="center-me">Weight</th>
                            </tr>
                            </thead>
                            <tbody>';
                foreach ($vet[0]->getPets() as $pet) {
                    if ($filter_by_date) {
                        // check start date
                        $post_date = convertToTimeStamp($pet->getPostDate());
                        $filter_start_date = convertToTimeStamp($_REQUEST['start_date']);
                        $filter_end_date = convertToTimeStamp($_REQUEST['end_date']);
                        if (($filter_start_date <= $post_date) && ($filter_end_date >= $post_date)) {
                            echo '
                                        <tr>
                                            <td>' . $pet->getCustomField('pet-tag-id') . '</td>
                                            <td>' . $pet->getPetType() . '</td>
                                            <td class="center-me">' . $pet->getCustomField('pet-weight') . 'kg</td>
                                        </tr>';
                        }
                    } else {
                        echo '
                                    <tr>
                                        <td>' . $pet->getCustomField('pet-tag-id') . '</td>
                                        <td>' . $pet->getPetType() . '</td>
                                        <td class="center-me">' . $pet->getCustomField('pet-weight') . 'kg</td>
                                    </tr>';
                    }
                }
                echo '
                            </tbody>
                        </table>
                    </td>
                </tr>';
                } else {
                   echo '<tr>
                        <td colspan="6" class="center-me">No pets</td>
                    </tr>';
                }
            $i++;
            }
            ?>
            </tbody>
        </table>
    </div>
</article>
