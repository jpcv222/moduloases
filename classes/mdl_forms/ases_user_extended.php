<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form for create user extended
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');require_once(__DIR__.'/../AsesUser.php');
require_once(__DIR__.'/../TrackingStatus.php');
require_once(__DIR__.'/../EstadoPrograma.php');
require_once(__DIR__.'/../Programa.php');

class ases_user_extended extends moodleform {
    public function definition()
    {

        /**
         *     public $id;
        public $id_moodle_user;
        public $id_ases_user;
        public $id_academic_program;
        public $tracking_status;
        public $program_status;
         */
        $mform = $this->_form;
        $tracking_status_options = TrackingStatus::get_options();
        $ases_user_options = AsesUser::get_options();

        $program_status_options = EstadoPrograma::get_options();
        $program_options = Programa::get_options();

        $mform->addElement('text', 'moodle_user_name', 'Nombre de usuario moodle');
        $mform->addElement('select', 'id_ases_user', 'Usuario ASES', $ases_user_options);
        $mform->addElement('checkbox', 'inactive_previus_track_stat', 'Desactivar seguimientos previos');

        $mform->addElement('select', 'tracking_status', 'Estado de seguimiento', $tracking_status_options);
        $mform->addElement('select', 'program_status', 'Estado de programa', $program_status_options);
        $mform->addElement('select', 'id_academic_program', 'Programa academico', $program_options);
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
    public function get_ases_user_extended() {

    }
    public function validation($data, $files): array {
        $ases_user_extended = new AsesUserExtended();
        $ases_user_extended->make_from($data);
        if($data['inactive_previus_track_stat']) {
            AsesUserExtended::disable_all_tracking_status($ases_user_extended->id_ases_user);
        }
        $errors = array();
        if(AsesUserExtended::have_active_tracking_status($ases_user_extended->id_ases_user)) {
            $errors[AsesUserExtended::ID_ASES_USER] = "El usuario ya tiene un seguimiento activo ";
        }
        return $errors;
    }
}