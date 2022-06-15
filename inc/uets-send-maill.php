<?php

add_action( 'wp_ajax_ajax_form_action', 'ajax_action_callback' );
add_action( 'wp_ajax_nopriv_ajax_form_action', 'ajax_action_callback' );

function ajax_action_callback() {

// Массив ошибок
$errors = [];

// Если не прошла проверка nonce, то блокируем отправку
if ( !wp_verify_nonce( $_POST['nonce'], 'uets-tests-ajax-form-nonce' ) ) {
wp_die( 'Данные отправлены с некорректного адреса' );
}

// Проверяем на спам. Если скрытое поле заполнено или снят чек, то блокируем отправку
if ( $_POST['form_anticheck'] === false || !empty( $_POST['form_submitted'] ) ) {
wp_die( 'Ты кто такой, давай, до свидания!' );
}


// Проверяем поле имени, если пустое, то пишем сообщение в массив ошибок
if ( empty( $_POST['form_name'] ) || !isset( $_POST['form_name'] ) ) {
$errors['name'] = 'Пожалуйста, введите ваше имя.';
} else {
$form_name = sanitize_text_field( $_POST['form_name'] );
}

// Проверяем поле ввода телефона, если пустое, то пишем сообщение в массив ошибок
if ( empty( $_POST['form_tel'] ) || !isset( $_POST['form_tel'] ) ) {
$errors['tel'] = 'Пожалуйста, введите номер телефона';
} else {
$form_tel = sanitize_text_field( $_POST['form_tel'] );
}

// Проверяем поле сообщения, если пустое, то пишем сообщение по умолчанию
if ( empty( $_POST['form_message'] ) || !isset( $_POST['form_message'] ) ) {
$form_message = 'Пустое сообщение с сайта';
} else {
$form_message = sanitize_text_field( $_POST['form_message'] );
}


// Проверяем массив ошибок, если не пустой, то передаем сообщение. Иначе отправляем письмо
if ( $errors ) {

wp_send_json_error( $errors );

} else {

// Узнаем с какого сайта пришло письмо
$home_url = wp_parse_url( home_url() );
$subject = 'Письмо с сайта ' . $home_url['host'];

// Указываем адресаты
$email_to = 'example@example.ru';
$email_from = get_option( 'admin_email' );

// Собираем письмо
$body  = 'Имя: ' . $form_name . '\n';
$body .= 'Телефон: ' . $form_tel . '\n';
$body .= 'Сообщение: ' . $form_message . '\n';

$headers = 'From: ' . $home_url['host'] . ' <' . $email_from . '>' . "\r\n" . 'Reply-To: ' . $email_from;

// Отправляем
wp_mail( $email_to, $subject, $body, $headers );

// Отправляем сообщение об успешной отправке
$message_success = 'Собщение отправлено. В ближайшее время мы с вами свяжемся';
wp_send_json_success( $message_success );
}

// Убиваем процесс ajax
wp_die();

}