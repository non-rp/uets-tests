jQuery(document).ready(function ($) {

    let form = $('.uets-tests-form'),
        pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

    form.find('.req-field').addClass('empty-field');

    function checkInput() {
        form.find('.req-field').each(function () {
            var el = $(this);
            if (el.hasClass('rf-mail')) {
                if (pattern.test(el.val())) {
                    el.removeClass('empty-field');
                } else {
                    el.addClass('empty-field');
                }
            } else if (el.val() != '') {
                el.removeClass('empty-field');
            } else {
                el.addClass('empty-field');
            }
        });
    }

    function lightEmpty() {
        form.find('.empty-field').addClass('rf-error');
        setTimeout(function () {
            form.find('.rf-error').removeClass('rf-error');
        }, 1000);
    }

    var $files = {}; // переменная. будет содержать данные файлов
    // заполняем переменную данными, при изменении значения поля file
    $('input[type=file]').on('change', function () {
        $files[this.name] = this.files;
        //console.log(this.files)
    });



    $(document).on('click', '#uets-tests-submit', function (e) {
        e.preventDefault();

        $('.test__radio-button .test__inputs').addClass('req');

        var formData = new FormData();
        // создадим пустой объект
        var $answers = {};
        // переберём все элементы input, textarea и select формы с id="myForm "
        $('#uets-tests-form').find('input, textarea, select').each(function () {
            if ($(this).is(':checked')) {
                $answers[this.name] = $(this).attr('data_question') + ' - ' + $(this).val();
            }

            if ($(this).hasClass('form-input-text')) {
                $answers[this.name] = $(this).attr('data_question') + ' - ' + $(this).val();
            }

  
        });

        // создадим пустой объект
        var $data = {};
        // переберём все элементы input, textarea и select формы с id="myForm "
        $('#uets-tests-form').find('input, textarea, select').each(function () {
            if ($(this).is(':checked')) {
                $data[this.name] = $(this).val();
            }
            if ($(this).hasClass('form-input-text')) {
                $data[this.name] = $(this).val();
            }
        });
        console.log($data);

        formData.append('first_name', $('.form-name').prop('value'));
        formData.append('last_name', $('.form-last-name').prop('value'));
        formData.append('patronymic', $('.form-patronymic').prop('value'));
        formData.append('tests_email', $('.form-tests-email').prop('value'));
        formData.append('admin_test_email', $('.admin-test-email').prop('value'));
        formData.append('message_to_user_email', $('.message-to-email').prop('value'));
        formData.append('test_title', $('.test-title').prop('value'));
        formData.append('answers', JSON.stringify($answers));
        formData.append('data', JSON.stringify($data));
        formData.append('nonce', ajax_form_object.nonce);
        $.each($files, function (key, value) {
            formData.append(key, value[0]);
        });


        $.ajax({
            type: 'POST',
            url: ajax_form_object.url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                form.addClass('is-sending');
            },
            error: function (response, status, error) {
                $('#uets-tests-submit').val('Что-то пошло не так...');
            },
            success: function (response) {
                form.removeClass('is-sending').addClass('is-sending-complete');

                if (response.success === true) {
                    // Если все поля заполнены, отправляем данные и меняем надпись на кнопке
                    form.after('<div class="notification notification_accept">' + response.data.message + '</div>').slideDown();
                    $('.testts-popup').show(100);
                    $('#uets-tests-form')[0].reset();
                    $('#form_submit').val('Отправить');
                } else {
                    // Если поля не заполнены, выводим сообщения и меняем надпись на кнопке
                    $.each(response.data, function (key, val) {
                        $('.form-' + key).addClass('error');
                        $('.form-' + key).after('<div class="notification notification_warning notification_warning_' + key + '" style="color: red;">' + val + '</div>');
                    });
                    $('#uets-tests-submit span').html('Что-то пошло не так...');
                }

                console.log(response);
            }
        });

    })


    $('#uets-tests-submit').on('click', function (e) {

        checkInput();

        var errorNum = form.find('.empty-field').length;

        if (errorNum > 0) {
            lightEmpty();
            e.preventDefault();
        }

    });


    $('.input-file').each(function () {
        var $input = $(this),
            $label = $input.next('.js-labelFile'),
            labelVal = $label.html();

        $input.on('change', function (element) {
            var fileName = '';
            if (element.target.value) fileName = element.target.value.split('\\').pop();
            fileName ? $label.addClass('has-file').find('.js-fileName').html(fileName) : $label.removeClass('has-file').html(labelVal);
        });
    });

});