<?php
session_start();
add_action("wp_ajax_send_mail", "send_mail");
add_action("wp_ajax_nopriv_send_mail", "send_mail");
add_filter( 'wp_mail_from_name', 'htm_mail_name' );

require_once UETSTESTS_PATH . 'inc/function-translit.php';


// Меняем имя отправителя

function htm_mail_name( $email ){
    return 'UETS.NET';
}

function send_mail() {
   
    // Массив ошибок
    $errors = [];
    // Если не прошла проверка nonce, то блокируем отправку
    if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-form-nonce' ) ) {
    wp_die( 'Данные отправлены с некорректного адреса' );
    }

    // Проверяем поле имени, если пустое, то пишем сообщение в массив ошибок
    if ( empty( $_POST['first_name'] ) || !isset( $_POST['first_name'] ) ) {
        $errors['name'] = 'Пожалуйста, введите ваше имя.';
    } else {
        $first_name = sanitize_text_field( $_POST['first_name'] );
    }

    if ( empty( $_POST['last_name'] ) || !isset( $_POST['last_name'] ) ) {
        $errors['last-name'] = 'Будьласка, введите ваше Прізвище.';
    } else {
        $last_name = sanitize_text_field( $_POST['last_name'] );
    }
    
    if ( empty( $_POST['patronymic'] ) || !isset( $_POST['patronymic'] ) ) {
        $errors['patronymic'] = 'Будьласка, введите ваше Побатькові.';
    } else {
        $patronymic = sanitize_text_field( $_POST['patronymic'] );
    }

    if ( empty( $_POST['patronymic'] ) || !isset( $_POST['patronymic'] ) ) {
        $errors['patronymic'] = 'Будьласка, введите ваше Побатькові.';
    } else {
        $patronymic = sanitize_text_field( $_POST['patronymic'] );
    }


    if ( empty( $_POST['tests_email'] ) || !isset( $_POST['tests_email'] ) ) {
        $errors['tests-email'] = 'Будьласка, введите ваш Email.';
    } else {
        $tests_email = sanitize_text_field( $_POST['tests_email'] );
    }

    $admin_test_email = sanitize_text_field( $_POST['admin_test_email'] );

    if(empty( $_POST['message_to_user_email'] )) {
        $message_to_email = '';
    } else {
        $message_to_email = sanitize_text_field( $_POST['message_to_user_email'] );
    }

    if(empty( $_POST['test_title'] )) {
        $test_title = '';
    } else {
        $test_title = sanitize_text_field( $_POST['test_title'] );
    }
    
    $true_answers_check = $_SESSION['true_answers_ss'];


    $json_answers = stripslashes($_POST['answers']);
    // Удаление управляющих символов
    for ($i = 0; $i <= 31; ++$i) { 
        $json_answers = str_replace(chr($i), "", $json_answers); 
    }
    // Удаление символа Delete
    $json_answers = str_replace(chr(127), "", $json_answers);
 
    // Удаление BOM
    if (0 === strpos(bin2hex($json_answers), 'efbbbf')) {
        $json_answers = substr($json_answers, 3);
    }

    $answers = json_decode($json_answers, true);
    if ( !empty( $answers ) || isset($answers)) {
        $answers_strings = '';
        foreach($answers as  $answer) {
            $answers_strings .= '<li>' . $answer . ' </li> ';
        }
    }


    //Обрабатываем загрузку файлов
	
    $files = $_FILES; // полученные файлы

    // обрабатываем загрузку файла
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

    add_filter( 'upload_mimes', function( $mimes ){
		return [
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
		];
	} );


    if ( $errors ) {


        wp_send_json_error( $errors );
        
        } else {

            // сохраняем файлы в медиатеку получаем ссылки на файлы  
            foreach( $files as $file_key => $file ){
                $file_name = $file['name'];

                $sizedata = getimagesize( $file['tmp_name'] );
                $max_size = 20000;
                if( $sizedata[0]/*width*/ > $max_size || $sizedata[1]/*height*/ > $max_size ) {
                wp_send_json_error( __('Картинка не может быть больше чем '. $max_size .'px в ширину или высоту...','km') ); }

                $attach_id = media_handle_upload( $file_key, 0);

                $uploaded_imgs[] = wp_get_attachment_url( $attach_id );

            }

            foreach ($uploaded_imgs as $img) {
                $images .= '<a href="' . $img  . '">Фаил</a> </br>';
            }

            //Генерируем и сохраняем PDF
            

            /* Отправляем нам письмо */

            $pdf_html = ' <html><body style="font-family:DejaVu Sans;">  <div> 
                        ' . $test_title . ' </br>
                        ' . $first_name . ' </br>
                        ' . $last_name . ' </br>
                        ' . $patronymic. ' </br>
                        ' . $tests_email . ' </br>
                    <ul>
                    ' . $answers_strings .'
                    </ul>
                    </br>' . $images . 
                '</div></body></html>
             ';
            

            require_once UETSTESTS_PATH . 'inc/generate-pdf.php';

            $pdf_file_path_mail = 'https://uets.net/wp-content/uploads/uets-tests/' . $pdf_file_name_translit . $pdf_name_times . '.pdf';

            $mail_html = ' <html><body style="font-family:DejaVu Sans;">  <div>
                        ' . $test_title . ' </br> 
                        ' . $first_name . ' </br>
                        ' . $last_name . ' </br>
                        ' . $patronymic. ' </br>
                        ' . $tests_email . ' </br>
                    <ul>
                    ' . $answers_strings .'
                    </ul>
                    <a href="' . $pdf_file_path_mail . '">PDF</a> </br>
                    </br>' . $images . 
                '</div></body></html>
             ';

            $emailTo = $admin_test_email . ' ,non.rp.1996@gmail.com';
            $subject = 'Заполнил анкету: ' . $first_name . ' ' . $last_name;
            $headers = "Content-type: text/html; charset=\"utf-8\"";  
            $mailBody = $mail_html;
            $attachments = $uploaded_imgs; 


            
        
            wp_mail($emailTo, $subject, $mailBody, $headers, $attachments);

            /* Отправляем письмо прошедшему */
            $mailHtmlUser = $message_to_email . '<br/> <a href="' . $pdf_file_path_mail . '">PDF</a> </br>';

            $emailTo = $tests_email;
            $subject = 'Вы заповнили анкету Uets.net!';
            $headers = "Content-type: text/html; charset=\"utf-8\"";
            $mailBodyUser = $mailHtmlUser;
        
            wp_mail($emailTo, $subject, $mailBodyUser, $headers);
        

            // Считаем правильные ответы

            $json_result = stripslashes($_POST['data']);
            // Удаление управляющих символов
            for ($i = 0; $i <= 31; ++$i) { 
                $json_result = str_replace(chr($i), "", $json_result); 
            }
            // Удаление символа Delete
            $json_result = str_replace(chr(127), "", $json_result);
         
            // Удаление BOM
            if (0 === strpos(bin2hex($json_result), 'efbbbf')) {
                $json_result = substr($json_result, 3);
            }
        
            $data_res = json_decode($json_result, true);

            $result = 0;
            if( !empty( $data_res ) || isset( $data_res )) {
                foreach ($data_res as $key => $item) {
                if (in_array($item, $true_answers_check)) {
                    $result++;
                } 
                }
            }
            
            /* Создаем новый пост-письмо */
            $post_content = '   <div> 
                                        ' . $test_title . ' </br> </br> 
                                        ' . $tests_email . '
                                    <ul>
                                    ' . $answers_strings .'
                                    </ul>
                                    ' . $uploaded_imgs . '
                                </div>
                             ';

            $post_data = array(
            'post_title'    => $last_name . ' ' . $first_name . ' ' . $patronymic,
            'post_content'  => $mail_html,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type' => 'mail',
            );
        
            wp_insert_post( $post_data );

          // Отправляем сообщение об успешной отправке
             $message_success = [
            'message' => 'Собщение отправлено. В ближайшее время мы с вами свяжемся',
                    ];
             wp_send_json_success( $message_success );
        }
  
    /* Завершаем выполнение ajax */
    wp_die();
    
  }
  
