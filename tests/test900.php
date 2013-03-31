<?php
/**
 * XML_Builder_Array用の全ロジックをテスト
 *
 */
return $builder

->root
    ->moge('str',array('attr'=>'attr'),'str')
    ->_
    ->moge1_('str','str')
    ->moge2('str')
        ->mogeChild_('abc')
        ->_text('str')
    ->_

    ->moge3
        ->mogeChild1_
        ->mogeChild2_
        ->_text('str')
    ->_

    ->moge4(array('attr'=>'attr'),'str')
        ->mogeChild1_
    ->_

    ->moge5(array('attr'=>'attr'))
        ->mogeChild1_
    ->_

    ->moge6
        ->mogeChild1_('a')
        ->mogeChild1_('b')
        ->mogeChild1_('c')
    ->_

    ->moge7
        ->abc_
        ->bcd_
        ->abc_
    ->_

    ->moge8
        ->abc_
        ->abc_
        ->bcd_
        ->abc_
    ->_

    ->moge9
        ->abc_
        ->bcd_
        ->abc_
        ->abc_
    ->_

->_;
