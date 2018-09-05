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
 * Ases block
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';

function dphpforms_dwarehouse(){
    global $DB;
    $msg = "UPDATE";
    $sql = "SELECT *   FROM {talentospilos_df_dwarehouse} AS df_dwarehouse WHERE df_dwarehouse.accion = $msg";
    
   
}

function get_array_for_reports($instance_id){
    $columns = array();
    array_push($columns, array("title"=>"Id usuario", "name"=>"id_usuario", "data"=>"1"));
    array_push($columns, array("title"=>"Acción", "name"=>"fullname", "data"=>"Hola"));
    array_push($columns, array("title"=>"Fecha y hora", "name"=>"fecha_hora", "data"=>"Hoy"));

    $data = array(
        "bsort" => false,
        "columns" => $columns,
        "language" => 
         array(
            "search"=> "Buscar:",
            "oPaginate" => array(
                "sFirst"=>    "Primero",
                "sLast"=>     "Último",
                "sNext"=>     "Siguiente",
                "sPrevious"=> "Anterior"
                ),
            "sProcessing"=>     "Procesando...",
            "sLengthMenu"=>     "Mostrar _MENU_ registros",
            "sZeroRecords"=>    "No se encontraron resultados",
            "sEmptyTable"=>     "Ningún dato disponible en esta tabla",
            "sInfo"=>           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty"=>      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered"=>   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix"=>    "",
            "sSearch"=>         "Buscar:",
            "sUrl"=>            "",
            "sInfoThousands"=>  ",",
            "sLoadingRecords"=> "Cargando...",
         ),
        "order"=> array(0, "desc")

    );

    return $data;

}