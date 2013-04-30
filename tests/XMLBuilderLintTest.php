<?php
class DummyObject {
    function __toString() {
        return 'DummyObject';
    }
}

class XMLBuilderLintTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    function ElementNameMustBeString() {
        $b = $this->newBuilder();

        $b->xmlElem(array());
        (string)$b;
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    function AttributesMustBeString() {
        $b = $this->newBuilder();

        $b->xmlElem('abc')
            ->xmlAttr(array(
                'moge' => array()
            ))
        ->_;
        (string)$b;
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    function CdataMustBeString() {
        $b = $this->newBuilder();

        $b->xmlCdata(array())
            ->_;
        (string)$b;
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    function processingInstructionMustBeString() {
        $b = $this->newBuilder();

        $b->xmlPi(fopen('php://input'))
            ->_;
        (string)$b;
    }

    /**
     * @test
     * @expectedException LogicException
     */
    function remains() {
        $b = $this->newBuilder();

        $b->root;
        $b->xmlRender();
    }

    /**
     * @test
     */
    function expectOK() {
        $b = $this->newBuilder();

        $b
            ->root
                ->bool_(true)
                ->float_(1.1)
                ->int_(5)
                ->string_('abc')
                ->xmlText('some string')
                ->xmlRaw('<string/>')
                ->xmlPi('php', 'echo "Hello world";')
                ->date_(new DateTime)
                ->object_(new DummyObject)
            ->_;
        ob_start();
        $b->_echo();
        $result = ob_get_clean();
        self::assertEquals('ok', $result);
    }

    protected function newBuilder() {
        return xml_builder(array('class'=>'lint'));
    }
}
