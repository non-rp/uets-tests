<?php 
session_start();
get_header(); 

require_once UETSTESTS_PATH . 'inc/function-translit.php';

?>

<div class="test">
    <div class="test__container">

    <h1 class="test__title"><?php the_title(); ?></h1>

    <p class="test__content"><?php the_content(); ?></p>


    <form  id="uets-tests-form" class="uets-tests-form" enctype="multipart/form-data" method="post">

        <input class="form__input form-name req-field" name="first-name" type="text" placeholder="Ім'я" required>
        <label class="form__label"  for="name">Ім'я</label>
 
        <input class="form__input form-last-name req-field" name="last-name" type="name" placeholder="Прізвище">
        <label class="form__label"  for="name">Прізвище</label>

        <input class="form__input form-patronymic req-field" name="patronymic" type="name" placeholder="Побатькові">
        <label class="form__label"  for="name">Побатькові</label>
        
        <input class="form__input form-tests-email req-field rf-mail" name="tests-email" type="email" placeholder="Email">
        <label class="form__label" for="email">Email</label>

        <input class="form__input admin-test-email" name="admin-test-email" type="hidden" value="<?php the_field('email_vidpovidalnogo'); ?>">

        <input class="form__input message-to-email" name="message-to-email" type="hidden" value="<?php the_field('text-on-email')['tekst_korystuvachu']; ?>">

        <input class="form__input test-title" name="test-title" type="hidden" value="<?php the_title(); ?>">
     <?php
     $true_answers = array();
            if( have_rows('tests')):

                $quant = 0;
                while ( have_rows('tests') ) : the_row();
                    $quant++;

                    // RADIO_TEST
                    if( get_row_layout() == 'radio_test' ): 
                        $pytannya = get_sub_field('pytannya');
                    ?>
                        <div class="test__radio-button"> 

                       <h3><?php echo  $pytannya; ?></h3>
                       <?
                        if( have_rows('varianty_vidpovidi') ): ?>
                            <div class="test__inputs">
                            <?php // перебираем строки повторителя
                            while ( have_rows('varianty_vidpovidi') ) : the_row();
                                $variant = get_sub_field('variant');
                                $true_variant = get_sub_field('virnij');
                                if ($true_variant) {
                                    $key = 'answer'. $quant . $i++;
                                    $true_answers[$key] = $variant;
                                   // var_dump($true_answers);
                                }
                                
                                ?>
                               <div> <input type="radio" id="test-radio" data_question="<?php echo $pytannya; ?>" name="<?php echo 'answer'. $quant; ?>"  value="<?php echo $variant; ?>" required>
                                     <label for="<?php echo $variant; ?>"><?php echo $variant ?></label>
                                </div>
                                <?php
                            endwhile; ?>
                            </div> <?php                              

                        endif; ?>
                         </div>  <?php
                    endif;
                    // RADIO TEST END


                     // CHECKBOX TEST
                     if( get_row_layout() == 'checkbox_test' ): 
                        $pytannya = get_sub_field('pytannya');
                     ?> 
                        <div class="test__radio-button"> 
                        <h3><?php echo $pytannya ?></h3>
                        <?
                         if( have_rows('varianty_vidpovidi') ): ?>
                             <div class="test__inputs">
                             <?php // перебираем строки повторителя
                             $i = 0;
                             while ( have_rows('varianty_vidpovidi') ) : the_row();
                                 $variant = get_sub_field('variant');
                                 $true_variant = get_sub_field('virnij');
                                if ($true_variant) {
                                    $key = 'answer'. $quant . $i++;
                                    $true_answers[$key] = $variant;
                                    //var_dump($true_answers);
                                }
                                 ?>
                                <div> <input type="checkbox" id="test-chekbox" data_question="<?php echo $pytannya ?>" name="<?php echo 'answer'. $quant . $i++; ?>" value="<?php echo $variant; ?>">
                                      <label for="<?php echo $variant; ?>"><?php echo $variant ?></label>
                                 </div>
                                 <?php
                             endwhile; ?>
                             </div> <?php                              
 
                         endif; ?>
                          </div>  <?php
                     endif;
                     // CHECKBOX TEST END

                     // Text inputs TEST
                     if( get_row_layout() == 'form_input_text' ):  

                     ?> 
                     <div class="test__radio-button"> 
                        <h3><?php echo (get_sub_field('question')); ?></h3>
                        <?
                          ?>
                             <div class="test__inputs">
                             <?php 
                                 $question = get_sub_field('question');    
                                 ?>
                                <div><input class="form__input form-input-text req-field" data_question="<?php echo  get_sub_field('question'); ?>" name="<?php echo $question ?>" type="text" data_inp="text" placeholder="<?php echo (get_sub_field('plejsholder')); ?>">
                                 </div>
                                 <?php
                             ?>
                             </div> <?php                              
 
                          ?>
                        </div>  <?php
                     endif;
 
                     // Text inputs TEST END

                      // Textarea inputs TEST
                      if( get_row_layout() == 'form_input_textarea' ):   ?> 
                        <div class="test__radio-button"> 
                           <h3><?php echo (get_sub_field('question')); ?></h3>
                           <?
                             ?>
                                <div class="test__inputs">
                                <?php 
                                    $question = get_sub_field('question');    
                                    ?>
                                   <div><textarea class="form__input-textarea form-input-text req-field" data_question="<?php echo get_sub_field('question'); ?>" name='<?php echo $question ?>' type="textarea" rows="4" cols="35" data_inp="text" placeholder="<?php echo (get_sub_field('plejsholder')); ?>"> </textarea>
                                    </div>
                                    <?php
                                ?>
                                </div> <?php                              
    
                             ?>
                           </div>  <?php
                        endif;
    
                        // Textarea inputs TEST END

                     // Upload file field

                     if( get_row_layout() == 'form_load_file' ):
                        ?> <div class="test__radio-button"> 
 
                        <h3><?php echo (get_sub_field('tekst')); ?></h3>
                        <?
                          ?>
                             <div class="test__inputs">
                             <?php // перебираем строки повторителя
                                 //$question = get_sub_field('question');    
                                 ?>
                                <div>
                                <input accept=".png, .jpg, .jpeg" type="file" name="<?php echo 'file' . $quant ?>" id="<?php echo 'file' . $quant ?>" class="input-file">
                                    <label for="<?php echo 'file' . $quant ?>" class="btn btn-tertiary js-labelFile">
                                        <i class="icon fa fa-check"></i>
                                        <span class="js-fileName">Завантажити файл</span>
                                    </label>
                                 </div>
                                 <?php
                             ?>
                             </div> <?php                              
 
                          ?>
                          </div>  <?php
                     endif;
                     
                     // Upload file 

                endwhile;

            else :

                // макетов не найдено

            endif; ?>

        <button id="uets-tests-submit" class="button">
	        <span class="submit">Відправити</span>
        </button>
    </form>
    </div>
    <?php 
    ?>
</div> 

<?php


$_SESSION["true_answers_ss"] = $true_answers; ?>
<?php
get_footer(); ?>

<div class="testts-popup test-popup-show">
    <div class="testts-popup__container">
        <?php if(get_field('tests_popup')['zagolovok']) { ?>
            <h2><?php echo get_field('tests_popup')['zagolovok']; ?></h2>
            <?php } 
            
            if(get_field('tests_popup')['tekst_popap']) {  ?>
            <p><?php echo get_field('tests_popup')['tekst_popap']; ?></p>
            <?php } ?>
            <a href="<?php echo get_field('tests_popup')['url']; ?>"><?php echo get_field('tests_popup')['title']; ?></a>
    </div>
</div>