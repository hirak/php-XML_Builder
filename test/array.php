<?php

require_once dirname(__FILE__) . '/../lib/XML/Builder.php';

$builder = xml_builder(array('class'=>'array'));

$builder
    ->html_('ahog');

echo $builder;


xml_builder(array('class'=>'array'))

    ->html_('ahog', array('abc'=>'dea'))
    ->html_('ahogege')

->_export($builder);


var_dump($builder->_dom);
