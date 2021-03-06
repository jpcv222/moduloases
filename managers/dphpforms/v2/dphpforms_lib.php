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
/*header('Content-Type: application/json');

$xQuery = new stdClass();
$xQuery->form = "seguimiento_pares"; // Can be alias(String) or idntifier(Number)
$xQuery->filterFields = [
                         ["id_estudiante",[
                             ["%%","LIKE"]
                            ], false],
                        ["fecha",[
                             ["%%","LIKE"]
                            ], false],
                        ["revisado_practicante",[
                            ["%%","LIKE"]
                           ], false],
                       ["revisado_profesional",[
                        ["%%","LIKE"]
                       ], false]
                   ];
$xQuery->orderFields = [
                        ["fecha","DESC"]
                       ];

$xQuery->orderByDatabaseRecordDate = false; // If true, orderField is ignored. DESC
$xQuery->recordStatus = [ "!deleted" ];// options "deleted" or "!deleted", can be both. Empty = both.
//No soportado aun
$xQuery->selectedFields = [ "id_creado_por", "id_estudiante" ]; // RecordId and BatabaseRecordDate are selected by default.

echo json_encode( dphpformsV2_find_records( $xQuery ) );*/

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
        $list_fields_id_alias = [];
        $list_fields_data_type = [];
        $list_valid_operators = ["=",">","<",">=","<=","!=", "LIKE"];
        foreach( $fields as $field ){
            array_push( $list_fields_alias, $field->local_alias );
            $list_fields_alias_id[$field->local_alias] = $field->id_pregunta;
            $list_fields_id_alias[$field->id_pregunta] = $field->local_alias;
            $list_fields_data_type[$field->id_pregunta] = $field->tipo_campo;
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
                };
                if( gettype( $filterField[2] ) !== "boolean" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT HAVE A VALID VALUE, USE bool true OR false NOT ". gettype( $filterField[2] ),
                        "data_response" => ""
                    ];
                };
                if( gettype( $filterField[1] ) !== "array" ){
                    return [
                        "status_code" => -1,
                        "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE. [...,[\"value\",\"operator\"],...]  ",
                        "data_response" => ""
                    ];
                }else{
                    foreach( $filterField[1] as $filterValues  ){
                        if( count( $filterValues ) != 2 ){
                            return [
                                "status_code" => -1,
                                "error_message" => "QUERY->filterFields: ".json_encode($filterValues)." DOES NOT MATCH WITH THE STRUCTURE. [\"value\",\"operator\"]  ",
                                "data_response" => ""
                            ];      
                        }else{
                            if( !in_array( $filterValues[1], $list_valid_operators ) ){
                                return [
                                    "status_code" => -1,
                                    "error_message" => "QUERY->filterFields: ".json_encode($filterValues)." DOES NOT HAVE A VALID OPERATOR, USE ".json_encode($list_valid_operators)." NOT ". $filterValues[1],
                                    "data_response" => ""
                                ];
                            }
                        }
                    }
                };
           }else{
            return [
                "status_code" => -1,
                "error_message" => "QUERY->filterFields: ".json_encode($filterField)." DOES NOT MATCH WITH THE STRUCTURE [\"alias_field\", \"value\", optional = true or false, operator = \">,<,=,!=,<=,>=\"]",
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


     $sql_query = "";
     //Find with where clause
     if( count( $query->filterFields ) > 0 ){

        $flag_deleted = false;
        $flag_non_deleted = false;
        foreach( $query->recordStatus as $status ){
            if( $status === "deleted" ){
                $flag_deleted = true;
            }elseif( $status === "!deleted" ){
                $flag_non_deleted = true;
            }
        }

        $status = "";
        if( !$flag_deleted && $flag_non_deleted ){
            $status = "1";
        }elseif( $flag_deleted && !$flag_non_deleted ){
            $status = "0";
        }
        
        $sql_first_parameter = "SELECT DISTINCT id AS id_formulario_respuestas
                                FROM {talentospilos_df_form_resp}";
        
        if( $status !== "" ){
            $sql_first_parameter .= " WHERE estado = $status";
        }

        $inner_join_more_responses = "SELECT id_respuesta, FS1.id_formulario_respuestas
                                      FROM {talentospilos_df_form_solu} AS FS1 
                                      INNER JOIN ( $sql_first_parameter ) AS PQ ON FS1.id_formulario_respuestas = PQ.id_formulario_respuestas 
                                      ORDER BY FS1.id_formulario_respuestas ASC";
        
        $inner_join_values = "SELECT R3.id, IJMR.id_formulario_respuestas, R3.respuesta, R3.id_pregunta, R3.fecha_hora_registro
                              FROM {talentospilos_df_respuestas} AS R3 
                              INNER JOIN ( $inner_join_more_responses ) AS IJMR ON id_respuesta = R3.id";

        $where_clause = "";
        if( count( $query->filterFields ) > 0 ){
            $where_clause = "WHERE ";
            $first_filter_field = true;

            $filter_fields = $query->filterFields;
            
            foreach( $filter_fields as $filterField ){

                $fieldAlias = $filterField[0];
                $filterValues = $filterField[1];
                $optional =  $filterField[2];

                $filter_where = "";
                $belongs_block_AND = false;
                
                if( !$first_filter_field ){
                    if( $tmpNextFilterField = next($filter_fields) ){
                        $filter_where .= " OR ";
                    }
                }else{
                    $first_filter_field = false;
                }

                if( $optional ){
                    $belongs_block_AND = false;
                }

                foreach( $filterValues as $filterValue ){
                    $filter_where .= "(id_pregunta = " .$list_fields_alias_id[$fieldAlias]. " AND respuesta ".$filterValue[1]." '". $filterValue[0] . "')";
                    if( next($filterValues) ){
                        $filter_where .= " AND ";
                    }
                }

                $where_clause .= $filter_where;

            }
    
        };

        $sql_query = $inner_join_values . " " . $where_clause;

     };

     //Grouping
     $records =  $DB->get_records_sql( $sql_query );
     $records_ids =  [];
     $grouped_records = [];
     foreach( $records as $record ){
        array_push( $records_ids, $record->id_formulario_respuestas );
        $grouped_records[ $record->id_formulario_respuestas ][ "fecha_hora_registro" ] = strtotime($record->fecha_hora_registro);
        $grouped_records[ $record->id_formulario_respuestas ][ "id_registro" ] = $record->id_formulario_respuestas;
        $grouped_records[ $record->id_formulario_respuestas ][ $list_fields_id_alias[ $record->id_pregunta ] ] = $record->respuesta;
     };

     $records_ids = array_values(array_unique( $records_ids ));

     //echo( $sql_query . "\n" );

     $valid_records = [];

     //Si el registro agrupado tiene los campos para filtrar
     foreach($records_ids as $record_id){
         
         $record_completed = true;
         foreach( $query->filterFields as $filterField ){
            $field_alias = $filterField[0];
            $id_field = $list_fields_alias_id[ $field_alias ];
            $value_to_comparate = $filterField[1];
            $optional = $filterField[2];
            $operator = $filterField[3];
            $exist_in_grouped_record = array_key_exists( $field_alias, $grouped_records[$record_id] );
            if( !$exist_in_grouped_record && !$optional ){
                $record_completed = false;
            }
         };
         if($record_completed){
             //array_push($valid_records,$record_id);
             array_push($valid_records,$grouped_records[$record_id]);
         }
     }

     if( !$query->orderByDatabaseRecordDate ){
        foreach ($query->orderFields as $orderField) {

            $alias = $orderField[0];
            $order = $orderField[1];
            $key_to_sort = array(); 

            foreach ($valid_records as $key => $record){
                $key_to_sort[$key] = $record[ $alias ];
            }
            if( strtoupper( $order ) === "ASC" ){
                array_multisort($key_to_sort, SORT_ASC, $valid_records);
            }elseif( strtoupper( $order ) === "DESC"  ){
                array_multisort($key_to_sort, SORT_DESC, $valid_records);
            }
        }   
     }else{
        $key_to_sort = array(); 
        foreach ($valid_records as $key => $record){
            $key_to_sort[$key] = $record[ "registered_timestamp" ];
        }
        array_multisort($key_to_sort, SORT_DESC, $valid_records);
     }

     //print_r( $valid_records );

     /*$sql = "";
     $filter = "";
     $ids = "";

     foreach( $query->selectedFields as $selectedField ){
        $filter .= "R.id_pregunta = " . $list_fields_alias_id[ $selectedField ];
        if( next( $query->selectedFields ) ){
            $filter .= " OR ";
        }
     }

     foreach( $valid_records as $record_id ){
        $ids .= "FS.id_formulario_respuestas = $record_id";
        if( next($valid_records) ){
            $ids .= " OR ";
        }
     }

     $sql .= "SELECT *
        FROM {talentospilos_df_respuestas} AS R
        INNER JOIN {talentospilos_df_form_solu} AS FS ON FS.id_respuesta = R.id
        WHERE ( $ids ) AND ( $filter )";

    $DB->get_records_sql( $sql );*/

    return $valid_records;

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
    FROM {talentospilos_df_form_preg} AS FP
    INNER JOIN (SELECT * FROM {talentospilos_df_preguntas} )AS P
    ON FP.id_pregunta = P.id
    INNER JOIN (SELECT * FROM {talentospilos_df_tipo_campo} ) AS TC
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

 
  function dphpformsV2_reverse_new_field_update( $form_id_alias, $id_pregunta, $default_value ){

    global $DB;

    $form_info = dphpformsV2_get_form_info( $form_id_alias );

    $records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE id_formulario = ( SELECT id FROM {talentospilos_df_formularios} WHERE alias = '" . $form_info->alias . "' AND estado = 1 ) 
    
    EXCEPT    
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id 
    WHERE R.id_pregunta = $id_pregunta";

    $records = $DB->get_records_sql( $records_to_update );

    $fails = [];
    $correct = [];

    foreach( $records as $key => $record ){

        $return = dphpformsv2_store_reverse_rield( $record->id_formulario_respuestas, $id_pregunta, $default_value );
        if( !$return ){
            array_push( $fails, $record->id_formulario_respuestas  );
        }else{
            array_push( $correct, $record->id_formulario_respuestas  );
        }
    }

    $to_return = new stdClass();
    $to_return->fails = $fails;
    $to_return->correct = $correct;

    return $to_return;

  }

  function dphpformsV2_get_records_reverse_new_field_update( $id_respuesta, $form_id_alias ){

    global $DB;

    $form_info = dphpformsV2_get_form_info( $form_id_alias );

    $records_to_update = "SELECT id AS id_formulario_respuestas
    FROM {talentospilos_df_form_resp} 
    WHERE id_formulario = ( SELECT id FROM {talentospilos_df_formularios} WHERE alias = '" . $form_info->alias . "' AND estado = 1 ) 
    
    EXCEPT    
    
    SELECT FS.id_formulario_respuestas 
    FROM {talentospilos_df_form_solu} AS FS 
    INNER JOIN {talentospilos_df_respuestas} AS R 
    ON FS.id_respuesta = R.id 
    WHERE R.id_pregunta = $id_respuesta";

    return $DB->get_records_sql( $records_to_update );

  }

function dphpformsv2_store_reverse_rield( $form_response_id, $id_pregunta, $value ){

    global $DB;

    $sql_form_solu_exist = 
    "SELECT FU.id FROM {talentospilos_df_form_solu} AS FU
    INNER JOIN {talentospilos_df_respuestas} AS R ON FU.id_respuesta = R.id
    WHERE R.id_pregunta = $id_pregunta AND FU.id_formulario_respuestas = $form_response_id";

    //If it does not exist.
    if( !$DB->get_record_sql( $sql_form_solu_exist ) ){
 
        $respuesta = dphpformsv2_store_respuesta( $id_pregunta, $value );
        if( $respuesta ){
            return dphpformsV2_store_form_soluciones( $form_response_id, $respuesta );
        }else{
            return null;
        }

    }
}

function dphpformsv2_store_respuesta( $id, $value ){
    
    global $DB;

    $obj_respuesta = new stdClass();
    $obj_respuesta->id_pregunta = $id;
    $obj_respuesta->respuesta = $value;

    $pregunta = dphpformsV2_get_pregunta( $id );

    if( $pregunta ){

        if( dphpformsV2_regex_validator( $id, $value )->status ){
            $respuesta_identifier = $DB->insert_record('talentospilos_df_respuestas', $obj_respuesta, $returnid=true, $bulk=false);
            return $respuesta_identifier;
        }
        
    }else{
        return null;
    }
}

function dphpformsV2_regex_validator( $id, $value ){

    global $DB;

    $to_return = new stdClass();
    $to_return->status = true;
    $to_return->human_readable = "";
    $to_return->example =  "";

    $pregunta_obj = dphpformsV2_get_pregunta( $id );
    $tipo_campo_obj = dphpformsV2_tipo_campo( $pregunta_obj->tipo_campo );

    $regex = $tipo_campo_obj->expresion_regular;

    if( $regex ){

        if( preg_match( $regex, $value ) == 0 ){

            $to_return = new stdClass();
            $to_return->status = false;
            $to_return->human_readable = $tipo_campo_obj->regex_legible_humanos;
            $to_return->example =  $tipo_campo_obj->ejemplo;
             
        }
    }

    return $to_return;

}

function dphpformsV2_get_pregunta( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_preguntas} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function dphpformsV2_tipo_campo( $id ){

    global $DB;

    $sql = "SELECT * FROM {talentospilos_df_tipo_campo} WHERE id = " . $id;
    return $DB->get_record_sql( $sql );

}

function dphpformsV2_store_form_soluciones($form_response_id, $respuesta_identifier){

    global $DB;

    $obj_form_soluciones = new stdClass();
    $obj_form_soluciones->id_formulario_respuestas = $form_response_id;
    $obj_form_soluciones->id_respuesta = $respuesta_identifier;
   
    $form_solucines_identifier = $DB->insert_record('talentospilos_df_form_solu', $obj_form_soluciones, $returnid=true, $bulk=false);
    return $form_solucines_identifier;

}


  

?>
