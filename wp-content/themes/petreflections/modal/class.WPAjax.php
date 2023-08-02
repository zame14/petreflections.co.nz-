<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 9/19/2022
 * Time: 10:58 AM
 */
class WPAjax
{
    private $success = 0;
    private $error = 0;
    private $response = 0;

    function __construct($function)
    {
        if (method_exists($this, $function)) {
            // Runt he function
            $this->$function();
        } else {
            $this->error = 1;
            $this->response = 'Function not found for ' . $function;
        }
        echo $this->getResponse();
        session_write_close();
        exit;
    }

    public function getResponse()
    {
        // Prepare response array
        $json = Array(
            'success' => $this->success,
            'error' => $this->error,
            'response' => $this->response
        );
        $output = $json['response'];

        return $output;
    }
    private function updateStatus()
    {
        $pet_id = $_REQUEST['pet_id'];
        $status = $_REQUEST['status'];
        $pet = new Pet($pet_id);

        //update post with the new status
        update_post_meta($pet->id(), 'wpcf-pet-status', $status);

        //check if ashes are being returned
        if($pet->getCustomField('pet-ashes-returned') == "yes") {
            //check if owner wants to be notified by email.
            $owner = $pet->getOwner();
            if($owner->getCustomField('customer-notified') == "email") {
                // send owner an email
                switch($status) {
                    case "In machine":
                        $pet->notificationEmail(2);
                        break;
                    case "Delivered to vets":
                        $pet->notificationEmail(3);
                        break;
                    case "Delivered to owner":
                        $pet->notificationEmail(4);
                        break;
                }
                $this->response = 2;
            } else {
                $this->response = 1;
            }
        }
        if($status == "Delivered to owner" || $status == "Delivered to vets") {
            $pet->updateDeliveryDate();
        }
        if($status == "Service complete") {
            $pet->updateServiceCompletedDate();
        }
    }
    private function showPetStatusTables()
    {
        $html = pet_table_status_view();
        $this->response = $html;
    }
    private function showPetTable()
    {
        $url = get_page_link(29);
        $this->response = $url;
    }
    private function updateReport1()
    {
        $date1 = $_REQUEST['date1'];
        $date2 = $_REQUEST['date2'];
        $date1_raw = convertToRawDate($date1);
        $date1_raw .= ' 00:00:00';
        $date2_raw = convertToRawDate($date2);
        $date2_raw .= ' 00:00:00';

        $i = 0;
        $exclude = array(1);
        $args = array(
            'exclude' => $exclude,
            'orderby' => 'title',
            'order' => 'ASC',
            'fields' => 'all'
        );
        $users = get_users($args);

        $html = '
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
            <tbody>';
            foreach($users as $user) {
            $vet = getVetByUserID($user->ID);
            $html .= '<tr>
                <td class="center-me"><span class="fa fa-plus table-btn" onclick="showTable(' . $i . ')"></span></td>
                <td>' . $vet[0]->getTitle() . '</td>
                <td class="center-me">2</td>
                <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-ashes-returned-in','urn') . '</td>
                <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-memorial-jewellery','yes') . '</td>
                <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-delivery-options','clinic') . '</td>
                <td class="center-me">' . $vet[0]->report_num_of('wpcf-pet-delivery-options','address') . '</td>
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
                        foreach($vet[0]->getPets() as $pet) {
                        $html .= '
                        <tr>
                            <td>' . $pet->getCustomField('pet-tag-id') . '</td>
                            <td>' . $pet->getPetType() . '</td>
                            <td class="center-me">' . $pet->getCustomField('pet-weight') . 'kg</td>
                        </tr>';
                        }
                        $html .= '
                        </tbody>
                    </table>
                </td>
            </tr>';
            $i++;
            }
            $html.= '</tbody>
        </table>';
        $vet = getVetByUserID(5);
        $this->response = print_r($vet);
    }
}