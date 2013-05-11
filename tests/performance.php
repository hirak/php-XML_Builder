<?php
/**
 * performance benchmark
 *
 */
require_once dirname(__FILE__) . '/../src/XML/Builder.php';

function makeBuilders() {
    return array(
        'dom' => xml_builder(),
        'xmlwriter' => xml_builder(array('class'=>'xmlwriter')),
        'array' => xml_builder(array('class' => 'array')),
        'serialize' => xml_builder(array('class' => 'serialize')),
        'json' => xml_builder(array('class'=>'json')),
        'jsonp' => xml_builder(array('class'=>'jsonp', 'callback' => 'CALLBACK')),
        'lint' => xml_builder(array('class'=>'lint')),
    );
}

echo '============ shorthand style ===============', PHP_EOL;

foreach (makeBuilders() as $name => $builder) {
    $start = microtime(true);

    $builder
        ->root
            ->xmlExport($b);
                for ($i=0; $i<10000; $i++) {
                    $b->node_($i);
                }
            $b
        ->_;

    (string)$builder;

    echo "$name:\t";
    echo microtime(true) - $start;
    echo PHP_EOL;
}

echo '============ standard style ===============', PHP_EOL;

foreach (makeBuilders() as $name => $builder) {
    $start = microtime(true);

    $builder
        ->root
            ->xmlExport($b);
                for ($i=0; $i<10000; $i++) {
                    $b->xmlElem('node')->xmlText($i)->xmlEnd();
                }
            $b
        ->_;

    (string)$builder;

    echo "$name:\t";
    echo microtime(true) - $start;
    echo PHP_EOL;
}
