// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_form_builder
  */

  define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

                function get_url_parameters(url){
                    var start_param_position = url.indexOf("?");
                    var params = "";
                    for(var i = start_param_position; i < url.length; i++){
                        params += url[i];
                    }
                    return params;
                }

                $('#dphpforms-redirect-new-form').click(function(){
                    window.location.href = "dphpforms_form_builder.php" + get_url_parameters(window.location.href);
                });

                $('#dphpforms-redirect-adm-alias').click(function(){
                    window.location.href = "dphpforms_alias_editor.php" + get_url_parameters(window.location.href);
                });

                $('#dphpforms-redirect-adm-forms').click(function(){
                    window.location.href = "dphpforms_form_editor.php" + get_url_parameters(window.location.href);
                });

                $('.btn-remove-form').click(function(){
                    var form_name = $(this).attr('data-form-name');
                    var form_id = $(this).attr('data-form-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está eliminando el formulario <strong><i>" + form_name + "</i></strong>, ¿desea continuar?, tenga en consideración que los alias asociados a las preguntas del formulario no serán eliminados.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar!'
                      }, function(isConfirm) {
                        if (isConfirm) {

                            $.get( "../managers/dphpforms/dphpforms_form_updater.php?function=delete_form&id_form=" + form_id, function( data ) {
                                var response = data;
                                if(response['status'] == 0){
                                    swal(
                                        {title:'Información',
                                        text: 'Eliminado',
                                        type: 'success'},
                                        function(){
                                            window.location.href = window.location.href;
                                        }
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }
                            });

                        }
                    });
                });

                $('.btn-remove-alias').click(function(){
                    var alias = $(this).attr('data-form-alias');
                    var alias_id = $(this).attr('data-form-id');
                    swal({
                        html:true,
                        title: 'Confirmación',
                        text: "<strong>Nota importante!</strong>: Está eliminando el alias <strong><i>" + alias + "</i></strong>, ¿desea continuar?, tenga en consideración que cualquier consulta que haga uso de este alias dejará de funcionar.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Eliminar!'
                      }, function(isConfirm) {
                        if (isConfirm) {

                            $.get( "../managers/dphpforms/dphpforms_form_updater.php?function=delete_alias&id_alias=" + alias_id, function( data ) {
                                var response = data;
                                if(response['status'] == 0){
                                    swal(
                                        {title:'Información',
                                        text: 'Eliminado',
                                        type: 'success'},
                                        function(){
                                            window.location.href = window.location.href;
                                        }
                                    );
                                }else if(response['status'] == -1){
                                    swal(
                                        'Error!',
                                        response['message'],
                                        'error'
                                    );
                                }
                            });

                        }
                    });
                });
            }
    };
      
})