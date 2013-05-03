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
     * @expectedException DomainException
     */
    function ElementNameMustBeString() {
        $b = $this->newBuilder();

        $b->xmlElem(array());
        (string)$b;
    }

    /**
     * @test
     * @expectedException DomainException
     */
    function RootNodeMustBeOne() {
        $b = $this->newBuilder();

        $b->root_;
        $b->root_;
    }

    /**
     * @test
     * @expectedException DomainException
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
     * @expectedException DomainException
     */
    function CdataMustBeString() {
        $b = $this->newBuilder();

        $b->xmlElem('abc')
            ->xmlCdata(array())
        ->_;
        (string)$b;
    }

    /**
     * @test
     * @expectedException DomainException
     */
    function processingInstructionMustBeString() {
        $b = $this->newBuilder();

        $b->xmlPi(array(), array());
        (string)$b;
    }

    /**
     * @test
     * @expectedException DomainException
     */
    function commentMustBeString() {
        $b = $this->newBuilder();

        $b->xmlComment(fopen('php://input', 'r'));
        (string)$b;
    }

    /**
     * @test
     * @expectedException DomainException
     */
    function commentMustNotHaveDoubleHyphen() {
        $b = $this->newBuilder();

        $b->xmlComment('hogehoge--');
        (string)$b;
    }

    /**
     * @test
     * @expectedException DomainException
     */
    function YouForgotEndingTag() {
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
            ->root(array('str'=>'string', 'xmlns'=>'hogehoge'))
                ->bool_(true)
                ->float_(1.1)
                ->int_(5)
                ->string_('abc')
                ->xmlText('some string')
                ->xmlRaw('<string/>')
                ->xmlPi('php', 'echo "Hello world";')
                ->xmlComment('test comment')
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
