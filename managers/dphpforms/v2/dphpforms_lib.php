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
 * @author     Jeison Cardona Gómez
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__). '/../../../../../config.php');
header('Content-Type: application/json');

$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)
$xQuery->filterFields = [["id_estudiante", "428", true],["id_creado_por", "value", true],["id_instancia", "value", false], ["id_monitor","value", false]];
$xQuery->orderFields = [["id_instancia","ASC"], ["id_creado_por", "DESC"]  ];
$xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored
$xQuery->likeFields  = [["id_estudiante", true], ["id_creado_por", true], ["id_instancia", true], ["id_monitor", true]];
$xQuery->recordStatus = [ "!deleted", "!deleted" ];// options "deleted" or "!deleted", can be both.
$xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // RecordId and BatabaseRecordDate are selected by default.

echo json_encode( dphpformsV2_find_records( $xQuery ) );

/**
 * 
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int 
 * @param int 
 * @return stdClass
 */
 function dphpformsV2_find_records( $query ){

    global $DB;

    $form = dphpformsV2_get_form_info( $query->form );
    
    if( $form ){
        $fields = dphpformsV2_get_fields_form( $form->id );
        $list_fields_alias = [];
        $list_fields_alias_id = [];
        foreach( $fields as $field ){
            array_push( $list_fields_alias, $field->local_alias );
            $list_fields_alias_id[$field->local_alias] = $field->id_pregunta;
        };
        //Validation if the filter fields exist.
        foreach( $query->filterFields as $filterField ){
           if( count( $filterField ) == 3 ){
                if( !in_array( $filterField[0], $list_fields_alias ) ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT EXIST AS A FIELD",
                        "data_response" => ""
                    ];
                }elseif( gettype( $filterField[2] ) !== "boolean" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $filterField[2] ),
                        "data_response" => ""
                    ];
                };
           }else{
            return [
                "status_code" => -1,
                "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"value\", optional = true or false]",
                "data_response" => ""
            ];
           };
        };
        //Validation if the order fields exist.
        foreach( $query->orderFields as $orderField ){
            if( count( $orderField ) == 2 ){
                 if( !in_array( $orderField[0], $list_fields_alias ) ){
                     return [
                         "status_code" => -1,
                         "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT EXIST AS A FIELD",
                         "data_response" => ""
                     ];
                 }else{
                     if( !((strtoupper($orderField[1]) == "ASC") || (strtoupper($orderField[1]) == "DESC") )){
                        return [
                            "status_code" => -1,
                            "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT HAVE A VALID VALUE, USE 'ASC' OR 'DESC'",
                            "data_response" => ""
                        ];
                     }
                 };
            }else{
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->orderFields: ".json_encode($orderField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"ASC OR DESC\"]",
                 "data_response" => ""
             ];
            };
         };
         //Validation if the like fields exist.
         foreach( $query->likeFields as $likeField ){
            if( count( $likeField ) == 2 ){
                 if( !in_array( $likeField[0], $list_fields_alias ) ){
                     return [
                         "status_code" => -1,
                         "error_message" => "QUERY->likeFields: ".json_encode($likeField)." DOES NOT EXIST AS A FIELD",
                         "data_response" => ""
                     ];
                 }else{
                     if( !( ($likeField[1] === true) || ($likeField[1] === false) ) ){
                        return [
                            "status_code" => -1,
                            "error_message" => "QUERY->likeFields: ".json_encode($likeField)." DOES NOT HAVE A VALID VALUE, USE true OR false",
                            "data_response" => ""
                        ];
                     };
                 };
            }else{
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->likeFields: ".json_encode($likeField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", true or false]",
                 "data_response" => ""
             ];
            };
         };
         //Validation if the likeFields are declared in the filterFields.
         foreach( $query->likeFields as $likeField ){
            $exist = false;
            foreach( $query->filterFields as $filterField ){
                if( $filterField[0] === $likeField[0] ){
                    $exist = true;
                };
            };
            if(!$exist){
                return [
                    "status_code" => -1,
                    "error_message" => "QUERY->likeFields: ".json_encode($likeField)." ARE NOT DECLARED IN QUERY->filterFields",
                    "data_response" => ""
                ];
            };
         };
         //Validation if the selected fields exist.
         foreach( $query->selectedFields as $selectedField ){
            if( !in_array( $selectedField, $list_fields_alias ) ){
                 return [
                     "status_code" => -1,
                     "error_message" => "QUERY->selectedFields: ".json_encode($selectedField)." DOES NOT EXIST AS A FIELD",
                     "data_response" => ""
                 ];
            };
         };

    }else{
        return [
            "status_code" => -1,
            "error_message" => "QUERY->form: $query->form DOES NOT EXIST",
            "data_response" => ""
        ];
    };

    if( gettype( $query->orderByDatabaseRecordDate ) !== "boolean" ){
        return [
            "status_code" => -1,
            "error_message" => "QUERY->orderByDatabaseRecordDate: $query->orderByDatabaseRecordDate DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $query->orderByDatabaseRecordDate ),
            "data_response" => ""
        ];
    };

    //Validation of record status
    foreach( $query->recordStatus as $rStatus ){
        $valid_values = [ "deleted", "!deleted" ];
        if( !in_array( $rStatus, $valid_values ) ){
             return [
                 "status_code" => -1,
                 "error_message" => "QUERY->recordStatus: ".json_encode($rStatus)." IS NOT A VALID VALUE",
                 "data_response" => ""
             ];
        };
     };

     //Validations completed

     //Find with where clause
     if( count( $query->filterFields ) > 0 ){

        //Find by the firt param the records id.
        $sql_first_parameter = "SELECT DISTINCT FS.id_formulario_respuestas
                                FROM {talentospilos_df_respuestas} AS R
                                INNER JOIN {talentospilos_df_form_solu} AS FS ON FS.id_respuesta = R.id
                                WHERE R.id_pregunta = ".$list_fields_alias_id[$query->filterFields[0][0]]." 
                                AND R.respuesta = '". $query->filterFields[0][1] . "'";
        
        $where_clause = "";
        if( count( $query->filterFields ) > 1 ){
            $first = true;
            foreach( $query->filterFields as $filterField ){
                if( $first ){
                    $first = false;
                    $where_clause  = "WHERE ( id_pregunta = ".$list_fields_alias_id[$query->filterFields[0][0]]." AND respuesta = '". $query->filterFields[0][1] . "' )";
                }else{
                    $operator = "AND"; 
                    if($filterField[2]){
                        $operator = "OR";
                    };
                    $where_clause .= " $operator ( id_pregunta = ".$list_fields_alias_id[$filterField[0]]." AND respuesta = '". $filterField[1] . "' )";
                }
            }
        };

        $inner_join_more_responses = "SELECT id_respuesta, FS1.id_formulario_respuestas
                                      FROM {talentospilos_df_form_solu} AS FS1 
                                      INNER JOIN ($sql_first_parameter) AS PQ ON FS1.id_formulario_respuestas = PQ.id_formulario_respuestas 
                                      ORDER BY FS1.id_formulario_respuestas";
        
        $inner_join_values = "SELECT * 
                              FROM {talentospilos_df_respuestas} AS R3 
                              INNER JOIN ( $inner_join_more_responses ) AS IJMR ON id_respuesta = R3.id";
        echo $inner_join_values;
        die();
        return $DB->get_records_sql( $sql_first_parameter );

     };
   
 }

 /**
 * Function that return the basic dynamic form information.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int/$string Alias or identifier 
 * @return stdClass
 */
 function dphpformsV2_get_form_info( $alias_identifier ){
    
    global $DB;

    $criteria = "id = $alias_identifier";
    if( !is_numeric( $alias_identifier ) ){
        $criteria = "alias = '$alias_identifier'";
    }

    $sql = "SELECT id, nombre, alias, descripcion, method, action, enctype, fecha_hora_registro, estado 
    FROM {talentospilos_df_formularios} 
    WHERE $criteria
    AND estado = 1";

    return $DB->get_record_sql( $sql );

 }

 /**
 * Function that return a list of forms by criteria.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int/$string Alias or identifier 
 * @return stdClass
 */
function dphpformsV2_get_find_forms( $column_name, $value, $using_like = false, $status = 1 ){
    
    global $DB;

    if( !$column_name || !$value ){
        return [];
    }

    $criteria = "$column_name = '$value'";
    if( $using_like == true ){
        $criteria = "LIKE $column_name '%$value%'";
    }

    $sql = "SELECT id, nombre, alias, descripcion, method, action, enctype, fecha_hora_registro, estado 
    FROM {talentospilos_df_formularios} 
    WHERE $criteria
    AND estado = $status";

    return $DB->get_records_sql( $sql );

 }

 /**
 * Function that return a list of form fields.
 * @author Jeison Cardona Gómez. <jeison.cardona@correounivalle.edu.co>
 * @param int $form_id 
 * @param int $status: 0 = deleted.
 * @return stdClass
 */
function dphpformsV2_get_fields_form( $form_id, $status = 1 ){
    
    global $DB;

    if( !is_numeric( $form_id ) && !is_numeric( $status )  ){
        return [];
    }

    $sql = 
    "SELECT FP.id AS id_formulario_pregunta, FP.id_pregunta, P.enunciado, TC.campo AS tipo_campo, FP.posicion, P.atributos_campo, P.opciones_campo, P.fecha_hora_registro 
    FROM mdl_talentospilos_df_form_preg AS FP
    INNER JOIN (SELECT * FROM mdl_talentospilos_df_preguntas )AS P
    ON FP.id_pregunta = P.id
    INNER JOIN (SELECT * FROM mdl_talentospilos_df_tipo_campo) AS TC
    ON P.tipo_campo = TC.id
    WHERE FP.id_formulario = $form_id
    AND FP.estado = $status
    ";

    $fields = $DB->get_records_sql( $sql );
    $fields = array_values( $fields );
    for( $i = 0; $i < count( $fields ); $i++ ){
        $atributos_campo = json_decode( $fields[$i]->atributos_campo );
        $opciones_campo = json_decode( $fields[$i]->opciones_campo );
        $fields[$i]->opciones_campo = $opciones_campo;
        $fields[$i]->atributos_campo = $atributos_campo;
        $fields[$i]->local_alias = $atributos_campo->local_alias;
    }

    return $fields;

 }


?>
